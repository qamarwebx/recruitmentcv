<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>Campaign Name</th>
            <th>Template</th>
            <th>SMTP</th>
            <th>Audience</th>
            <th>Careoff</th>
            <th>Group</th>
            <th>Schedule Date</th>
            <th>Status</th>
            <th>Subject</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                @php
                    // ✅ Careoff Name Handling
                    if ($post->admin_id) {
                        $careoffUsers = DB::table('admins')
                            ->where('status', 1)
                            ->whereIn('id', explode(',', $post->admin_id))
                            ->pluck('name')
                            ->toArray();

                        $newcareoffname = implode(', ', $careoffUsers);
                        $careoffname = strlen($newcareoffname) > 15
                            ? substr($newcareoffname, 0, 15) . '...'
                            : $newcareoffname;
                    } else {
                        $careoffname = '---';
                        $newcareoffname = '---';
                    }

                    // ✅ Group Name Handling (Contactp / Allcontact)
                    if ($post->group_id) {
                        if ($post->audience === 'contactp') {
                            $groups = DB::table('groupms')
                                ->whereIn('id', explode(',', $post->group_id))
                                ->pluck('name')
                                ->toArray();
                        } elseif ($post->audience === 'allcontact') {
                            $groups = DB::table('groupallcs')
                                ->whereIn('id', explode(',', $post->group_id))
                                ->pluck('name')
                                ->toArray();
                        } else {
                            $groups = [];
                        }
                        $group_name = $groups ? implode(', ', $groups) : '---';
                    } else {
                        $group_name = '---';
                    }

                    // ✅ Email Status Badge
                    $status = strtolower($post->email_status ?? '');
                    $badgeClass = match ($status) {
                        'success' => 'badge bg-label-success',
                        'scheduled' => 'badge bg-label-warning',
                        'failed' => 'badge bg-label-danger',
                        default => 'badge bg-label-secondary',
                    };
                    $statusLabel = ucfirst($status ?: 'Pending');

                    // ✅ Subject Handling
                    $subject = $post->email_subject ?: '---';
                @endphp

                <tr>
                    <!-- Campaign Name -->
                    <td>
                        @if (Auth::guard('admin')->user()->user_type == 2 && isset($perm) && $perm->view_email_campaign == 0)
                            {{ $post->campaign_name }}
                        @else
                            <a href="{{ route('admin.emailCampaign.show', $post->id) }}" target="_blank">
                                {{ $post->campaign_name }}
                            </a>
                        @endif
                    </td>

                    <!-- Template Name -->
                    <td>
                        {{ $post->template->template_name ?? '---' }}
                    </td>

                    <!-- SMTP -->
                    <td>
                        {{ $post->smtp->smtp_name ?? '---' }}
                    </td>

                    <!-- Audience -->
                    <td>{{ ucfirst($post->audience ?? '---') }}</td>

                    <!-- Careoff -->
                    <td title="{{ $newcareoffname ?? '---' }}">{{ $careoffname ?? '---' }}</td>

                    <!-- Group -->
                    <td>{{ $group_name ?? '---' }}</td>

                    <!-- Schedule Date -->
                    <td>
                        {{ $post->schedule_datetime
                            ? \Carbon\Carbon::parse($post->schedule_datetime)->format('d-m-Y H:i')
                            : '---' }}
                    </td>

                    <!-- Status -->
                    <td><span class="{{ $badgeClass }}">{{ $statusLabel }}</span></td>

                    <!-- Subject -->
                    <td>{{ Str::limit($subject, 40) }}</td>

                    <!-- Actions -->
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.emailCampaign.show', $post->id) }}" target="_blank" class="text-body">
                                <i class="ti ti-eye ti-sm me-2"></i>
                            </a>
                            <a href="javascript:;" 
                               class="text-body delete_email_campaign"
                               data-bs-toggle="modal" 
                               data-bs-target="#deleteEmailCampaign"
                               data-id="{{ $post->id }}">
                                <i class="ti ti-trash ti-sm mx-2"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" class="text-center text-muted py-3">No Email Campaigns Found</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Pagination -->
<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
