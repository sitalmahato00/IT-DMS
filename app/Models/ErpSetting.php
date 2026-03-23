<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ErpSetting extends Model
{
    protected $table = 'erp_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key with fallback
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );
    }

    /**
     * Get all settings in a group as key-value array
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn($s) => [$s->key => static::castValue($s->value, $s->type)])
            ->toArray();
    }

    /**
     * Cast value to appropriate type
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match($type) {
            'integer' => (int) $value,
            'float'   => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }

    /**
     * Default settings with descriptions
     */
    public static function defaults(): array
    {
        return [
            // Grading
            ['key' => 'grade_a_plus_min', 'value' => '90', 'group' => 'grading', 'type' => 'integer', 'label' => 'A+ Minimum %', 'description' => 'Minimum percentage for A+ grade'],
            ['key' => 'grade_a_min', 'value' => '80', 'group' => 'grading', 'type' => 'integer', 'label' => 'A Minimum %'],
            ['key' => 'grade_b_plus_min', 'value' => '70', 'group' => 'grading', 'type' => 'integer', 'label' => 'B+ Minimum %'],
            ['key' => 'grade_b_min', 'value' => '60', 'group' => 'grading', 'type' => 'integer', 'label' => 'B Minimum %'],
            ['key' => 'grade_c_plus_min', 'value' => '50', 'group' => 'grading', 'type' => 'integer', 'label' => 'C+ Minimum %'],
            ['key' => 'grade_c_min', 'value' => '40', 'group' => 'grading', 'type' => 'integer', 'label' => 'C Minimum %'],
            ['key' => 'grade_d_min', 'value' => '35', 'group' => 'grading', 'type' => 'integer', 'label' => 'D Minimum %'],
            ['key' => 'grade_pass_min', 'value' => '40', 'group' => 'grading', 'type' => 'integer', 'label' => 'Pass Minimum %'],
            // Attendance
            ['key' => 'min_attendance_percent', 'value' => '75', 'group' => 'attendance', 'type' => 'integer', 'label' => 'Minimum Attendance %', 'description' => 'Minimum required attendance percentage'],
            ['key' => 'late_threshold_minutes', 'value' => '15', 'group' => 'attendance', 'type' => 'integer', 'label' => 'Late Threshold (mins)'],
            ['key' => 'attendance_approval_required', 'value' => '0', 'group' => 'attendance', 'type' => 'boolean', 'label' => 'Require Admin Approval'],
            // Semester
            ['key' => 'semester_duration_weeks', 'value' => '16', 'group' => 'semester', 'type' => 'integer', 'label' => 'Default Semester Duration (weeks)'],
            ['key' => 'max_credits_per_semester', 'value' => '24', 'group' => 'semester', 'type' => 'integer', 'label' => 'Max Credits per Semester'],
            // Elective
            ['key' => 'max_electives_per_student', 'value' => '2', 'group' => 'elective', 'type' => 'integer', 'label' => 'Max Electives per Student'],
            ['key' => 'elective_approval_required', 'value' => '1', 'group' => 'elective', 'type' => 'boolean', 'label' => 'Require Admin Approval for Electives'],
            ['key' => 'elective_enrollment_open', 'value' => '0', 'group' => 'elective', 'type' => 'boolean', 'label' => 'Global Elective Enrollment Open'],
        ];
    }
}
