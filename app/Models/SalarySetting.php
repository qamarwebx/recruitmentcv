<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    protected $fillable = [
        'office_start_time',
        'qualifying_late_end_time',
        'late_window_end_time',
        'half_day_after_time',
        'free_late_count',
        'free_late_max_minutes',
        'late_deduction_percent',
        'deduction_calculation_type',
        'deduction_slab_amount',
        'deduction_per_slab',
        'retroactive_late_deduction',
        'sat_absent_sunday_deduction',
        'mon_absent_prev_sunday_deduction',
        'updated_by',
    ];

    protected $casts = [
        'retroactive_late_deduction' => 'boolean',
        'sat_absent_sunday_deduction' => 'boolean',
        'mon_absent_prev_sunday_deduction' => 'boolean',
        'late_deduction_percent' => 'float',
        'deduction_slab_amount' => 'float',
        'deduction_per_slab' => 'float',
    ];

    /**
     * The 3 valid values for `deduction_calculation_type`.
     */
    public const DEDUCTION_TYPE_PERCENTAGE = 'percentage';
    public const DEDUCTION_TYPE_FIXED_SLAB = 'fixed_slab';
    public const DEDUCTION_TYPE_FIXED_AMOUNT = 'fixed_amount';

    public const DEDUCTION_TYPES = [
        self::DEDUCTION_TYPE_FIXED_SLAB,
        self::DEDUCTION_TYPE_PERCENTAGE,
        self::DEDUCTION_TYPE_FIXED_AMOUNT,
    ];

    /**
     * This is a single-row configuration table. A row is seeded by the
     * create_salary_settings_table migration, so first() should always find
     * one - the firstOrCreate() fallback only guards against that row being
     * deleted out of band.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
