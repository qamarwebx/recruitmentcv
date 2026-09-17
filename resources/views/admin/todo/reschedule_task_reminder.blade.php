@extends('layout.admin.admin_layout')

@section('title','Reschedule Task Reminder')

@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}">
@endsection


@section('content')
<div class="container mt-4">
    <h2>Reschedule Task Reminder</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.todo.reminder_reschedule_update') }}" method="POST">
        @csrf
        <input type="hidden" name="task_id" value="{{ $task->id }}">

        <div class="row">

            <!-- Task Title -->
<div class="col-md-12 mb-2">
    <label><b>Task Title:</b> {{ $task->task_title }}</label>
</div>

<!-- Task Description -->
<div class="col-md-12 mb-2">
    <label><b>Task Description:</b> {{ $task->task_description }}</label>
</div>

<!-- Reminder Type -->
<div class="col-md-12 mb-2">
    <label><b>Reminder Type:</b> {{ $task->reminder_type }}</label>
</div>

<!-- Reminder Details -->
<div class="col-md-12 mb-2">
    @if($task->reminder_type === 'OneTime')
        <label><b>Scheduled Date & Time:</b> {{ $task->scheduled_date_time }}</label>
    @elseif($task->reminder_type === 'Recurring')
        @php
            $recurringInfo = $task->recurring_type;
            switch ($task->recurring_type) {
                case 'Daily':
                    $recurringInfo .= ' - '.$task->recurring_time;
                    break;
                case 'Weekly':
                    $days = $task->recurring_weekdays ? implode(', ', json_decode($task->recurring_weekdays, true)) : '';
                    $recurringInfo .= " ($days) - ".$task->recurring_time;
                    break;
                case 'Monthly':
                    $recurringInfo .= " (Day ".$task->recurring_month_day.") - ".$task->recurring_time;
                    break;
                case 'Yearly':
                    $recurringInfo .= " (".$task->recurring_year_month_day.") - ".$task->recurring_time;
                    break;
            }
        @endphp
        <label><b>Recurring Details:</b> {{ $recurringInfo }}</label>
    @elseif($task->reminder_type === 'Custom')
        <label><b>Custom Reminder:</b> {{ $task->custom_start_date.' to '.$task->custom_end_date.' at '.$task->custom_time }}</label>
    @endif
</div>


            @php
                use Carbon\Carbon;

                $options = [
                    15=>'15 minutes', 30=>'30 minutes', 45=>'45 minutes', 60=>'1 hour',
                    120=>'2 hours', 180=>'3 hours', 240=>'4 hours', 300=>'5 hours',
                    360=>'6 hours', 420=>'7 hours', 480=>'8 hours', 540=>'9 hours'
                ];

                // Parse reminder_at safely
                $reminderAt = $task->reminder_at ? Carbon::parse($task->reminder_at) : null;

                // Determine which option should be selected
                $selectedOption = null;
                if (is_numeric($task->reminder_before) && array_key_exists($task->reminder_before, $options)) {
                    $selectedOption = $task->reminder_before;
                } elseif ($task->reminder_before === 'next_day') {
                    $selectedOption = 'next_day';
                } elseif ($task->reminder_before === 'custom') {
                    $selectedOption = 'custom';
                }
            @endphp

            <!-- Reminder Before Task -->
            <div class="col-md-12 mb-2">
                <label for="reminder_before_option" class="form-label"><b>Remind me After</b></label>
                <select name="reminder_before_option" id="reminder_before_option" class="form-select">
                    @foreach($options as $minutes => $label)
                        <option value="{{ $minutes }}" {{ $selectedOption == $minutes ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                    <option value="next_day" {{ $selectedOption == 'next_day' ? 'selected' : '' }}>Next Day</option>
                    <option value="custom" {{ $selectedOption == 'custom' ? 'selected' : '' }}>Custom DateTime</option>
                </select>
            </div>

            <!-- Custom / Next Day DateTime Picker -->
            <div class="col-md-12 mb-2" id="custom_reminder_at_container" style="display:none;">
                <label for="custom_reminder_at" class="form-label"><b>Select Date & Time</b></label>
                <input type="text" name="custom_reminder_at" id="custom_reminder_at" class="form-control jsCustomDateTimePicker" 
                    value="{{ $reminderAt ? $reminderAt->format('Y-m-d H:i') : '' }}">
            </div>

        </div>

        <button type="submit" class="btn btn-primary">Update Reminder</button>
        <a href="{{ route('admin.todo.list') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
$(document).ready(function () {

    // Initialize flatpickr
    const dateTimePicker = $(".jsCustomDateTimePicker").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minDate: "today"
    });

    // Function to toggle visibility and handle Next Day logic
    function toggleCustomReminder() {
        const option = $('#reminder_before_option').val();
        const $container = $('#custom_reminder_at_container');
        const $input = $('#custom_reminder_at');

        if (option === 'next_day' || option === 'custom') {
            $container.show();

            // If user selects "Next Day", auto-fill tomorrow’s date with current time
            if (option === 'next_day') {
                const now = new Date();
                const tomorrow = new Date(now);
                tomorrow.setDate(now.getDate() + 1);
                const year = tomorrow.getFullYear();
                const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
                const day = String(tomorrow.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const formatted = `${year}-${month}-${day} ${hours}:${minutes}`;
                dateTimePicker.setDate(formatted, true);
            }
        } else {
            $container.hide();
            $input.val('');
        }
    }

    // Trigger on change and on load
    $('#reminder_before_option').on('change', toggleCustomReminder);
    toggleCustomReminder();
});
</script>
@endsection
    