<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatMessageAttachment;
use App\Models\ChatParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    const MESSAGES_PER_PAGE = 30;
    const PRESENCE_ONLINE_SECONDS = 90;
    const PRESENCE_WRITE_THROTTLE_SECONDS = 60;
    const TYPING_TTL_SECONDS = 6;
    const ATTACHMENT_MAX_KB = 10240; // 10 MB

    /**
     * Full /admin/chat page shell. All data loads via AJAX from here on.
     */
    public function index()
    {
        return view('admin.chat.index');
    }

    /**
     * Sidebar list: every active admin (per Admin::isActiveAdmin scope) except
     * myself, decorated with any existing direct conversation, last message
     * preview, unread count, and online status. Sorted like WhatsApp - most
     * recently active conversation first, admins with no history after.
     */
    public function conversations(Request $request)
    {
        $me = Auth::guard('admin')->user();
        $this->touchPresence($me);

        $term = trim((string) $request->query('q', ''));

        $admins = Admin::isActiveAdmin()
            ->where('id', '!=', $me->id)
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'profile', 'last_seen_at']);

        if ($admins->isEmpty()) {
            return $this->ok('', ['conversations' => []]);
        }

        $myConversations = ChatConversation::where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('admin_id', $me->id))
            ->with([
                'lastMessage' => fn ($q) => $q->with('attachments'),
                'participants' => fn ($q) => $q->where('admin_id', '!=', $me->id),
            ])
            ->get()
            ->keyBy(fn ($c) => optional($c->participants->first())->admin_id);

        $myParticipantRows = ChatParticipant::where('admin_id', $me->id)
            ->whereIn('conversation_id', $myConversations->pluck('id'))
            ->get()
            ->keyBy('conversation_id');

        $unreadByConversation = $this->unreadCountsByConversation($me->id);

        $rows = $admins->map(function (Admin $admin) use ($myConversations, $myParticipantRows, $unreadByConversation) {
            /** @var ChatConversation|null $conversation */
            $conversation = $myConversations->get($admin->id);
            $unreadCount = 0;
            $isUnreadManual = false;
            $lastMessagePreview = null;
            $lastMessageAt = null;

            if ($conversation) {
                $participant = $myParticipantRows->get($conversation->id);
                $isUnreadManual = (bool) optional($participant)->is_unread_manual;
                $unreadCount = $unreadByConversation->get($conversation->id, 0);
                $lastMessageAt = optional($conversation->last_message_at)->toIso8601String();

                if ($conversation->lastMessage) {
                    $lastMessagePreview = $this->messagePreview($conversation->lastMessage);
                }
            }

            return [
                'admin_id'          => $admin->id,
                'name'              => $admin->name,
                'avatar_url'        => $admin->avatar_url,
                'is_online'         => $admin->is_online,
                'conversation_id'   => optional($conversation)->id,
                'last_message'      => $lastMessagePreview,
                'last_message_at'   => $lastMessageAt,
                'unread_count'      => $unreadCount,
                'is_unread'         => $unreadCount > 0 || $isUnreadManual,
            ];
        });

        $sorted = $rows->sort(function ($a, $b) {
            if ($a['last_message_at'] === null && $b['last_message_at'] === null) {
                return strcmp($a['name'], $b['name']);
            }
            if ($a['last_message_at'] === null) {
                return 1;
            }
            if ($b['last_message_at'] === null) {
                return -1;
            }
            return strcmp($b['last_message_at'], $a['last_message_at']);
        })->values();

        return $this->ok('', ['conversations' => $sorted]);
    }

    /**
     * Find-or-create the direct conversation between me and the given admin.
     * Never creates a duplicate for the same pair.
     */
    public function start(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $request->validate([
            'admin_id' => 'required|integer|exists:admins,id',
        ]);

        $targetId = (int) $request->input('admin_id');

        if ($targetId === $me->id) {
            return $this->fail('You cannot start a conversation with yourself.', 422);
        }

        if (! Admin::isActiveAdmin()->where('id', $targetId)->exists()) {
            return $this->fail('That admin is not available for chat.', 404);
        }

        $conversation = DB::transaction(function () use ($me, $targetId) {
            $existing = ChatConversation::betweenAdmins($me->id, $targetId)->first();
            if ($existing) {
                return $existing;
            }

            $conversation = ChatConversation::create([
                'type'       => 'direct',
                'created_by' => $me->id,
            ]);

            $now = now();
            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'admin_id'        => $me->id,
                'joined_at'       => $now,
            ]);
            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'admin_id'        => $targetId,
                'joined_at'       => $now,
            ]);

            return $conversation;
        });

        $target = Admin::find($targetId);

        return $this->ok('Conversation ready.', [
            'conversation_id' => $conversation->id,
            'admin'           => [
                'admin_id'   => $target->id,
                'name'       => $target->name,
                'avatar_url' => $target->avatar_url,
                'is_online'  => $target->is_online,
            ],
        ]);
    }

    /**
     * Paginated messages for a conversation. Pass before_id to load older
     * messages (scroll-up), after_id to poll for new ones. With neither, the
     * latest page is returned.
     */
    public function messages(ChatConversation $conversation, Request $request)
    {
        $me = Auth::guard('admin')->user();
        $participant = $this->authorizeParticipant($conversation->id, $me->id);
        if ($participant === null) {
            return $this->fail('Conversation not found.', 403);
        }

        $query = ChatMessage::where('conversation_id', $conversation->id)
            ->with(['sender:id,name,profile', 'attachments', 'replyTo']);

        $beforeId = $request->query('before_id');
        $afterId = $request->query('after_id');

        if ($afterId) {
            $messages = $query->where('id', '>', (int) $afterId)
                ->orderBy('id')
                ->limit(self::MESSAGES_PER_PAGE)
                ->get();
        } else {
            if ($beforeId) {
                $query->where('id', '<', (int) $beforeId);
            }
            $messages = $query->orderByDesc('id')
                ->limit(self::MESSAGES_PER_PAGE)
                ->get()
                ->sortBy('id')
                ->values();
        }

        $otherTyping = false;
        $otherAdminId = ChatParticipant::where('conversation_id', $conversation->id)
            ->where('admin_id', '!=', $me->id)
            ->value('admin_id');
        if ($otherAdminId) {
            $otherTyping = (bool) Cache::get($this->typingKey($conversation->id, $otherAdminId));
        }

        return $this->ok('', [
            'messages'    => $messages->map(fn ($m) => $this->messageToArray($m)),
            'has_more'    => $messages->count() >= self::MESSAGES_PER_PAGE,
            'other_typing' => $otherTyping,
        ]);
    }

    /**
     * Send a text message (optionally a reply).
     */
    public function send(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $validated = $request->validate([
            'conversation_id'      => 'required|integer|exists:chat_conversations,id',
            'body'                 => 'required|string|max:5000',
            'reply_to_message_id'  => 'nullable|integer|exists:chat_messages,id',
        ]);

        $body = trim($validated['body']);
        if ($body === '') {
            return $this->fail('Message cannot be empty.', 422);
        }

        $conversationId = (int) $validated['conversation_id'];
        if ($this->authorizeParticipant($conversationId, $me->id) === null) {
            return $this->fail('Conversation not found.', 403);
        }

        $message = ChatMessage::create([
            'conversation_id'     => $conversationId,
            'sender_id'           => $me->id,
            'body'                => $body,
            'message_type'        => 'text',
            'reply_to_message_id' => $validated['reply_to_message_id'] ?? null,
            'is_deleted'          => false,
        ]);

        $this->touchLastMessage($conversationId, $message);
        Cache::forget($this->typingKey($conversationId, $me->id));

        $message->load(['sender:id,name,profile', 'attachments', 'replyTo']);

        return $this->ok('Message sent.', ['message' => $this->messageToArray($message)]);
    }

    /**
     * Upload + send a file attachment (image or document), with optional caption.
     */
    public function attachment(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $validated = $request->validate([
            'conversation_id'     => 'required|integer|exists:chat_conversations,id',
            'file'                => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip|max:' . self::ATTACHMENT_MAX_KB,
            'caption'             => 'nullable|string|max:2000',
            'reply_to_message_id' => 'nullable|integer|exists:chat_messages,id',
        ]);

        $conversationId = (int) $validated['conversation_id'];
        if ($this->authorizeParticipant($conversationId, $me->id) === null) {
            return $this->fail('Conversation not found.', 403);
        }

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid()->toString() . '.' . $ext;
        $path = $file->storeAs('chat/' . $conversationId, $storedName, 'public');

        $messageType = in_array($ext, ChatMessageAttachment::IMAGE_EXTENSIONS, true) ? 'image' : 'document';

        $message = ChatMessage::create([
            'conversation_id'     => $conversationId,
            'sender_id'           => $me->id,
            'body'                => $validated['caption'] ?? null,
            'message_type'        => $messageType,
            'reply_to_message_id' => $validated['reply_to_message_id'] ?? null,
            'is_deleted'          => false,
        ]);

        ChatMessageAttachment::create([
            'message_id'    => $message->id,
            'disk'          => 'public',
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => $storedName,
            'mime_type'     => $file->getMimeType(),
            'extension'     => $ext,
            'size'          => $file->getSize(),
        ]);

        $this->touchLastMessage($conversationId, $message);
        Cache::forget($this->typingKey($conversationId, $me->id));

        $message->load(['sender:id,name,profile', 'attachments', 'replyTo']);

        return $this->ok('File sent.', ['message' => $this->messageToArray($message)]);
    }

    public function markRead(ChatConversation $conversation)
    {
        $me = Auth::guard('admin')->user();
        $participant = $this->authorizeParticipant($conversation->id, $me->id);
        if ($participant === null) {
            return $this->fail('Conversation not found.', 403);
        }

        $lastMessageId = ChatMessage::where('conversation_id', $conversation->id)->max('id');

        $participant->last_read_message_id = $lastMessageId;
        $participant->last_read_at = now();
        $participant->is_unread_manual = false;
        $participant->save();

        return $this->ok('Marked as read.');
    }

    public function markUnread(ChatConversation $conversation)
    {
        $me = Auth::guard('admin')->user();
        $participant = $this->authorizeParticipant($conversation->id, $me->id);
        if ($participant === null) {
            return $this->fail('Conversation not found.', 403);
        }

        $participant->is_unread_manual = true;
        $participant->save();

        return $this->ok('Marked as unread.');
    }

    public function typing(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $validated = $request->validate([
            'conversation_id' => 'required|integer|exists:chat_conversations,id',
            'state'           => 'required|boolean',
        ]);

        $conversationId = (int) $validated['conversation_id'];
        if ($this->authorizeParticipant($conversationId, $me->id) === null) {
            return $this->fail('Conversation not found.', 403);
        }

        $key = $this->typingKey($conversationId, $me->id);
        if ($validated['state']) {
            Cache::put($key, true, now()->addSeconds(self::TYPING_TTL_SECONDS));
        } else {
            Cache::forget($key);
        }

        return $this->ok();
    }

    /**
     * Global badge count, polled from the floating widget on every admin page.
     */
    public function unreadCount()
    {
        $me = Auth::guard('admin')->user();
        $this->touchPresence($me);

        $counts = $this->unreadCountsByConversation($me->id);
        $manualUnreadConversations = ChatParticipant::where('admin_id', $me->id)
            ->where('is_unread_manual', true)
            ->pluck('conversation_id');

        $total = $counts->sum();
        foreach ($manualUnreadConversations as $conversationId) {
            if (($counts->get($conversationId, 0)) === 0) {
                $total += 1;
            }
        }

        return $this->ok('', ['count' => $total]);
    }

    public function adminSearch(Request $request)
    {
        $me = Auth::guard('admin')->user();
        $term = trim((string) $request->query('q', ''));

        $admins = Admin::isActiveAdmin()
            ->where('id', '!=', $me->id)
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email', 'profile', 'last_seen_at']);

        return $this->ok('', [
            'admins' => $admins->map(fn ($a) => [
                'admin_id'   => $a->id,
                'name'       => $a->name,
                'avatar_url' => $a->avatar_url,
                'is_online'  => $a->is_online,
            ]),
        ]);
    }

    public function deleteMessage(ChatMessage $message)
    {
        $me = Auth::guard('admin')->user();

        if ($this->authorizeParticipant($message->conversation_id, $me->id) === null) {
            return $this->fail('Message not found.', 403);
        }

        $isOwner = $message->sender_id === $me->id;
        $isModerator = $me->user_type == 1 || (bool) optional($me->adminpermission)->full_access;

        if (! $isOwner && ! $isModerator) {
            return $this->fail('You cannot delete another admin\'s message.', 403);
        }

        $message->is_deleted = true;
        $message->save();

        return $this->ok('Message deleted.');
    }

    public function viewAttachment(ChatMessageAttachment $attachment)
    {
        return $this->streamAttachment($attachment, false);
    }

    public function downloadAttachment(ChatMessageAttachment $attachment)
    {
        return $this->streamAttachment($attachment, true);
    }

    private function streamAttachment(ChatMessageAttachment $attachment, bool $forceDownload)
    {
        $me = Auth::guard('admin')->user();
        $message = $attachment->message;

        if (! $message || $message->is_deleted) {
            abort(404);
        }

        if ($this->authorizeParticipant($message->conversation_id, $me->id) === null) {
            abort(403);
        }

        if (! Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404);
        }

        $headers = [];
        if (! $forceDownload) {
            $headers['Content-Disposition'] = 'inline; filename="' . addslashes($attachment->original_name) . '"';
        }

        return Storage::disk($attachment->disk)->response(
            $attachment->path,
            $attachment->original_name,
            $headers
        );
    }

    // -------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------

    private function authorizeParticipant(int $conversationId, int $adminId): ?ChatParticipant
    {
        return ChatParticipant::where('conversation_id', $conversationId)
            ->where('admin_id', $adminId)
            ->first();
    }

    private function touchLastMessage(int $conversationId, ChatMessage $message): void
    {
        ChatConversation::whereKey($conversationId)->update([
            'last_message_id' => $message->id,
            'last_message_at' => $message->created_at,
        ]);
    }

    private function touchPresence(Admin $admin): void
    {
        if (! $admin->last_seen_at || $admin->last_seen_at->lt(now()->subSeconds(self::PRESENCE_WRITE_THROTTLE_SECONDS))) {
            Admin::whereKey($admin->id)->update(['last_seen_at' => now()]);
            $admin->last_seen_at = now();
        }
    }

    private function typingKey(int $conversationId, int $adminId): string
    {
        return "chat_typing_{$conversationId}_{$adminId}";
    }

    /**
     * Single grouped query: unread message count per conversation for this
     * admin, using the last_read_message_id high-water-mark instead of a
     * per-message read table.
     */
    private function unreadCountsByConversation(int $adminId)
    {
        return DB::table('chat_participants as cp')
            ->join('chat_messages as cm', function ($join) use ($adminId) {
                $join->on('cm.conversation_id', '=', 'cp.conversation_id')
                    ->on('cm.id', '>', DB::raw('COALESCE(cp.last_read_message_id, 0)'))
                    ->where('cm.sender_id', '!=', $adminId)
                    ->where('cm.is_deleted', false);
            })
            ->where('cp.admin_id', $adminId)
            ->select('cp.conversation_id', DB::raw('COUNT(*) as unread_count'))
            ->groupBy('cp.conversation_id')
            ->pluck('unread_count', 'conversation_id');
    }

    private function messagePreview(ChatMessage $message): string
    {
        if ($message->is_deleted) {
            return 'This message was deleted';
        }
        if ($message->message_type === 'image') {
            return '📷 Photo';
        }
        if ($message->message_type === 'document') {
            return '📎 ' . ($message->attachments->first()->original_name ?? 'Document');
        }
        return Str::limit((string) $message->body, 80);
    }

    private function messageToArray(ChatMessage $message): array
    {
        return [
            'id'                  => $message->id,
            'conversation_id'     => $message->conversation_id,
            'sender_id'           => $message->sender_id,
            'sender_name'         => optional($message->sender)->name,
            'sender_avatar'       => optional($message->sender)->avatar_url,
            'body'                => $message->display_body,
            'message_type'        => $message->message_type,
            'is_deleted'          => $message->is_deleted,
            'reply_to_message_id' => $message->reply_to_message_id,
            'reply_to'            => $message->replyTo ? [
                'id'   => $message->replyTo->id,
                'body' => $message->replyTo->display_body,
            ] : null,
            'attachments'         => $message->attachments->map(fn ($a) => [
                'id'            => $a->id,
                'original_name' => $a->original_name,
                'is_image'      => $a->is_image,
                'human_size'    => $a->human_size,
                'view_url'      => route('admin.chat.attachments.view', $a->id),
                'download_url'  => route('admin.chat.attachments.download', $a->id),
            ]),
            'created_at'          => $message->created_at->toIso8601String(),
            'created_at_human'    => $message->created_at->format('g:i A'),
        ];
    }

    private function ok(string $message = '', $data = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    private function fail(string $message, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], $status);
    }
}
