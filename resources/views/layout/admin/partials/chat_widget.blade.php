@php
    $__chatAdmin = Auth::guard('admin')->user();
    $__chatPermission = $__chatAdmin ? \App\Models\Adminpermission::where('staff_id', $__chatAdmin->id)->first() : null;
    $__hasChatAccess = $__chatAdmin && ($__chatAdmin->user_type == 1 || optional($__chatPermission)->full_access || optional($__chatPermission)->chat);
@endphp

@if ($__hasChatAccess)
<script>
    window.ChatConfig = {
        me: {
            id: {{ $__chatAdmin->id }},
            name: @json($__chatAdmin->name),
        },
        urls: {
            conversations: "{{ route('admin.chat.conversations') }}",
            start: "{{ route('admin.chat.conversations.start') }}",
            messages: "{{ route('admin.chat.conversations.messages', ['conversation' => '__ID__']) }}",
            send: "{{ route('admin.chat.messages.send') }}",
            attachment: "{{ route('admin.chat.messages.attachment') }}",
            deleteMessage: "{{ route('admin.chat.messages.delete', ['message' => '__ID__']) }}",
            read: "{{ route('admin.chat.conversations.read', ['conversation' => '__ID__']) }}",
            unread: "{{ route('admin.chat.conversations.unread', ['conversation' => '__ID__']) }}",
            typing: "{{ route('admin.chat.typing') }}",
            unreadCount: "{{ route('admin.chat.unread_count') }}",
            adminSearch: "{{ route('admin.chat.admins.search') }}",
        },
    };
</script>

@unless (request()->routeIs('admin.chat.*'))
    <div id="qchatFloating">
        <button type="button" class="qchat-fab" id="qchatToggleBtn" title="Chat">
            <i class="ti ti-message-circle-2"></i>
            <span class="qchat-fab-badge" id="qchatFabBadge" hidden>0</span>
        </button>
        <div class="qchat-panel" id="qchatPanel" hidden>
            <div class="qchat" id="qchatWidgetRoot" data-mode="widget"></div>
        </div>
    </div>

    <script>
        (function () {
            var $btn = $('#qchatToggleBtn');
            var $panel = $('#qchatPanel');
            var $badge = $('#qchatFabBadge');
            var mounted = false;

            $(document).on('qchat:unread', function (e, count) {
                if (count > 0) {
                    $badge.text(count > 99 ? '99+' : count).prop('hidden', false);
                } else {
                    $badge.prop('hidden', true);
                }
            });

            if (window.ChatEngine) {
                window.ChatEngine.mountBadge();
            }

            $btn.on('click', function () {
                var opening = $panel.prop('hidden');
                $panel.prop('hidden', !opening);
                if (opening && !mounted && window.ChatEngine) {
                    window.ChatEngine.mount('#qchatWidgetRoot', 'widget');
                    mounted = true;
                }
            });
        })();
    </script>
@endunless
@endif
