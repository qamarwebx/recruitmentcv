<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Todo extends Model
{
    use HasFactory;

    protected $casts = [
        'notification_type' => 'array',
    ];

    /**
     * Get the admin te Todo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the todolabel that owns the Todo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function todolabel()
    {
        return $this->belongsTo(Todolabel::class);
    }

    /**
     * Get the department that owns the Todo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }



    public function scopeFilterDepartment($query, $department_id){
        return $query->when($department_id, function ($query, $department_id) {
            $query->whereIn('department_id', (array) $department_id);
        });
    }

    public function scopeFilterLabel($query, $todolabel_id){
        return $query->when($todolabel_id, function ($query, $todolabel_id) {
            $query->whereIn('todolabel_id', (array) $todolabel_id);
        });
    }

    public function scopeFilterReminderCycle($query, $reminder_cycle){
        return $query->when($reminder_cycle, function ($query, $reminder_cycle) {
            $query->whereIn('reminder_cycle', (array) $reminder_cycle);
        });
    }

    public function scopeFilterType($query, $type){
        return $query->when($type, function($query,$type){
            $query->whereIn('type',(array)$type);
        });
    }

    public function scopeFilterTaskStatus($query, $task_status) {
        return $query->when($task_status, function ($query, $task_status) {
            $query->whereIn('task_status', (array) $task_status);
        });
    }

    /**
     * "Follow-up Due" bucket — same is_completed/finish_on definition
     * TodoDashboardService already uses for overdue/due-today/upcoming KPIs.
     */
    public function scopeFilterFollowupDue($query, $bucket)
    {
        return $query->when($bucket, function ($query, $bucket) {
            $query->where('is_completed', 0)->whereNotNull('finish_on');

            if ($bucket === 'before_today') {
                $query->whereDate('finish_on', '<', Carbon::today());
            } elseif ($bucket === 'today') {
                $query->whereDate('finish_on', '=', Carbon::today());
            } elseif ($bucket === 'upcoming') {
                $query->whereDate('finish_on', '>', Carbon::today());
            }
        });
    }

    public function scopeFilterAssignedTo($query, $assignto_id)
    {
        return $query->when($assignto_id, function ($query, $assignto_id) {
            $assignto_ids = is_array($assignto_id) ? $assignto_id : [$assignto_id];
            $query->where(function ($q) use ($assignto_ids) {
                foreach ($assignto_ids as $id) {
                    $q->orWhereRaw("FIND_IN_SET(?, assignto_id)", [$id]);
                }
            });
        });
    }

    public function scopeFilterFollowupBefore($query, $followup_before)
    {
        return $query->when($followup_before, function ($query, $followup_before) {
    
            $daysArray = is_array($followup_before) ? $followup_before : [$followup_before];
    
            $query->where(function ($q) use ($daysArray) {
    
                foreach ($daysArray as $days) {
    
                    $days = (int) $days;
    
                    $from = Carbon::now()->subDays($days)->startOfDay();
                    $to   = Carbon::now()->endOfDay();
    
                    // Special case for today
                    if ($days == 1) {
                        $from = Carbon::now()->startOfDay();
                        $to   = Carbon::now()->endOfDay();
                    }
    
                    $q->orWhereBetween('updated_at', [$from, $to]);
                }
    
            });
    
        });
    }
    
    public function scopeFilterCreatedBy($query, $created_by_id)
    {
        return $query->when($created_by_id, function ($query, $created_by_id) {
            $created_by_ids = is_array($created_by_id) ? $created_by_id : [$created_by_id];
            $query->where(function ($q) use ($created_by_ids) {
                foreach ($created_by_ids as $id) {
                    $q->orWhereRaw("FIND_IN_SET(?, admin_id)", [$id]);
                }
            });
        });
    }


    public function scopeFilterTodayReminderTask($query, $flag)
    {
        if ($flag != 1) {
            return $query;
        }

        $query->Where('task_status','!=','Achieved')->where('task_status','!=','Complete');

        $today = Carbon::today();
        $todayDate = $today->format('Y-m-d');
    
        // Convert today weekday → Mon, Tue, Wed...
        $todayWeekdayShort = $today->format('D'); // "Mon", "Tue"
    
        $todayMonthDay = $today->format('d');     // 01–31
        $todayYearMD = $today->format('m-d');     // MM-DD
    
        return $query->where(function($q) 
            use ($todayDate, $todayWeekdayShort, $todayMonthDay, $todayYearMD) {
                
            /*----------------------------------------------------
             | 1️⃣ One-Time: match only the DATE
             ----------------------------------------------------*/
            $q->orWhere(function($qq) use ($todayDate) {
                $qq->where('reminder_type', 'OneTime')
                   ->whereDate('scheduled_date_time', $todayDate)
                   ->where('scheduled_status', 1)
                   ->where('is_completed', 0);
            });
    
            /*----------------------------------------------------
             | 2️⃣ Recurring: Daily (always included today)
             ----------------------------------------------------*/
            $q->orWhere(function($qq) {
                $qq->where('reminder_type', 'Recurring')
                   ->where('recurring_type', 'Daily');
            });
    
            /*----------------------------------------------------
             | 3️⃣ Recurring: Weekly (stored as ["Mon","Tue"])
             ----------------------------------------------------*/
            $q->orWhere(function($qq) use ($todayWeekdayShort) {
                $qq->where('reminder_type', 'Recurring')
                   ->where('recurring_type', 'Weekly')
                   ->whereRaw("JSON_CONTAINS(recurring_weekdays, '\"$todayWeekdayShort\"')");
            });
    
            /*----------------------------------------------------
             | 4️⃣ Recurring: Monthly
             ----------------------------------------------------*/
            $q->orWhere(function($qq) use ($todayMonthDay) {
                $qq->where('reminder_type', 'Recurring')
                   ->where('recurring_type', 'Monthly')
                   ->where('recurring_month_day', $todayMonthDay);
            });
    
            /*----------------------------------------------------
             | 5️⃣ Recurring: Yearly
             ----------------------------------------------------*/
            $q->orWhere(function($qq) use ($todayYearMD) {
                $qq->where('reminder_type', 'Recurring')
                   ->where('recurring_type', 'Yearly')
                   ->where('recurring_year_month_day', $todayYearMD);
            });
    
            /*----------------------------------------------------
             | 6️⃣ Custom Range
             ----------------------------------------------------*/
            $q->orWhere(function($qq) use ($todayDate) {
                $qq->where('reminder_type', 'Custom')
                   ->whereDate('custom_start_date', '<=', $todayDate)
                   ->whereDate('custom_end_date', '>=', $todayDate);
            });
    
        });
    }
    

    // Other scopes for date ranges and search text
    public function scopeFilterDate($query, $column, $date)
    {
        return $query->when($date, function ($query) use ($column, $date) {
            $query->whereDate($column, $date);
        });
    }



    public function scopeFilterDateRange($query, $column, $date_range)
    {
        return $query->when($date_range, function ($query) use ($column, $date_range) {
            [$start, $end] = array_map('trim', explode('-', $date_range));
            $query->whereBetween($column, [date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end))]);
        });
    }

    public function scopefilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('task_title', 'like', $searchText)
                ->orWhere('task_description', 'like', $searchText)
                ->orWhereHas('department', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('todolabel', fn($q) => $q->where('name', 'like', $searchText))
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

    public function todoNotes()  
    {
        return $this->hasMany(TodoNote::class, 'deal_id');
    }

}
