@if($reminders->count() > 0)

<div class="col-md-12">
    <div class="notes-table-wrapper"
         style="max-height: 350px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">

        <table class="table table-bordered mb-0">
            <thead style="position: sticky; top: 0; z-index: 5; background-color: #fff;">
                <tr>
                    <th>Reminder Time</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($reminders as $reminder)

                    <tr>

                        <!-- Reminder Time -->
                        <td>
                            {{ \Carbon\Carbon::parse($reminder->reminder_at)->format('d M Y h:i A') }}
                        </td>

                        <!-- Description -->
                        <td>
                            {{ $reminder->whatsapp_description ?? '' }}
                        </td>

                        <!-- Status -->
                        <td>
                            @if($reminder->is_done)
                                <span class="badge bg-success">Done</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>

                        <!-- Created By -->
                        <td>
                            {{ optional($reminder->user)->name ?? 'N/A' }}
                        </td>

                        <!-- Created At -->
                        <td>
                            {{ \Carbon\Carbon::parse($reminder->created_at)->format('d M Y h:i A') }}
                        </td>

                        <!-- Actions -->
                        <td>

                        <a href="javascript:void(0);" class="delete-reminder" data-id="{{ $reminder->id }}">
                                                <i class="ti ti-trash ti-sm text-danger"></i>
                                            </a>
                         
                        </td>

                    </tr>

                @endforeach
            </tbody>
        </table>
    </div>
</div>

@else

<div class="text-center text-muted py-3">
    No reminders found
</div>

@endif