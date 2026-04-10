<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimetableSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'timetable_slots';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'semester',
        'section',
        'academic_year',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'slot_type',
        'lab_group',
        'group_type',
        'is_active',
        'is_locked',
        'locked_at',
        'is_holiday',
        'holiday_date',
        'max_capacity',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'is_holiday' => 'boolean',
        'holiday_date' => 'date',
        'locked_at' => 'datetime',
    ];

    /**
     * Get the subject for this slot
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the teacher for this slot
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Get slot type badge color
     */
    public function getSlotTypeColorAttribute(): string
    {
        return match($this->slot_type) {
            'theory'    => 'bg-blue-100 text-blue-700 border-blue-200',
            'practical' => 'bg-green-100 text-green-700 border-green-200',
            'tutorial'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'elective'  => 'bg-purple-100 text-purple-700 border-purple-200',
            default     => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Get lab group color for multiple groups
     */
    public function getLabGroupColorAttribute(): string
    {
        $colors = [
            'A' => 'bg-rose-100 text-rose-700 border-rose-200',
            'B' => 'bg-orange-100 text-orange-700 border-orange-200',
            'C' => 'bg-amber-100 text-amber-700 border-amber-200',
            'D' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'E' => 'bg-teal-100 text-teal-700 border-teal-200',
            'F' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            'G' => 'bg-sky-100 text-sky-700 border-sky-200',
            'H' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'I' => 'bg-violet-100 text-violet-700 border-violet-200',
            'J' => 'bg-fuchsia-100 text-fuchsia-700 border-fuchsia-200',
        ];
        
        $group = $this->lab_group ?? 'A';
        return $colors[$group] ?? 'bg-green-100 text-green-700 border-green-200';
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->start_time)->format('g:i A');
        $end   = \Carbon\Carbon::parse($this->end_time)->format('g:i A');
        return "{$start} – {$end}";
    }

    /**
     * Get short time format
     */
    public function getShortTimeRangeAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->start_time)->format('H:i');
        $end   = \Carbon\Carbon::parse($this->end_time)->format('H:i');
        return "{$start}-{$end}";
    }

    /**
     * Get day of week display label
     */
    public function getDayLabelAttribute(): string
    {
        return ucfirst($this->day_of_week ?? '');
    }

    /**
     * Get formatted lab group label
     */
    public function getLabGroupLabelAttribute(): string
    {
        if (!$this->lab_group) return '';
        return "Lab {$this->lab_group}";
    }

    /**
     * Check if this is a shared lecture (no lab group)
     */
    public function getIsSharedAttribute(): bool
    {
        return empty($this->lab_group) || $this->slot_type === 'theory';
    }

    /**
     * Check if slot is locked
     */
    public function getIsEditableAttribute(): bool
    {
        return !$this->is_locked;
    }

    /**
     * Scope for a given semester
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope for a given section
     */
    public function scopeForSection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope for a given day
     */
    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Scope: active slots only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_holiday', false);
    }

    /**
     * Scope: unlocked slots only
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope: for a specific time range
     */
    public function scopeInTimeRange($query, $startTime, $endTime)
    {
        return $query->where('start_time', '<', $endTime)
                     ->where('end_time', '>', $startTime);
    }

    /**
     * Scope: theory slots only
     */
    public function scopeTheory($query)
    {
        return $query->where('slot_type', 'theory');
    }

    /**
     * Scope: practical slots only
     */
    public function scopePractical($query)
    {
        return $query->where('slot_type', 'practical');
    }

    /**
     * Check if this slot conflicts with another slot (same teacher, overlapping time)
     */
    public function conflictsWith(TimetableSlot $other): bool
    {
        // Can't conflict with itself
        if ($this->id === $other->id) return false;
        
        // Must be same day
        if ($this->day_of_week !== $other->day_of_week) return false;
        
        // Must have same teacher
        if (!$this->teacher_id || !$other->teacher_id) return false;
        if ($this->teacher_id !== $other->teacher_id) return false;
        
        // Must have overlapping time
        return $this->start_time < $other->end_time && $this->end_time > $other->start_time;
    }

    /**
     * Find all teacher conflicts for this slot
     */
    public function findTeacherConflicts()
    {
        return TimetableSlot::where('id', '!=', $this->id)
            ->where('day_of_week', $this->day_of_week)
            ->where('teacher_id', $this->teacher_id)
            ->where('is_active', true)
            ->inTimeRange($this->start_time, $this->end_time)
            ->with(['subject', 'teacher.user'])
            ->get();
    }

    /**
     * Find room conflicts for this slot
     */
    public function findRoomConflicts()
    {
        if (!$this->room) return collect();
        
        return TimetableSlot::where('id', '!=', $this->id)
            ->where('day_of_week', $this->day_of_week)
            ->where('room', $this->room)
            ->where('is_active', true)
            ->inTimeRange($this->start_time, $this->end_time)
            ->with(['subject', 'teacher.user'])
            ->get();
    }

    /**
     * Get all time slots as a formatted array
     */
    public static function getTimeSlots(): array
    {
        return [
            ['start' => '08:00', 'end' => '09:00', 'label' => '8:00 - 9:00'],
            ['start' => '09:00', 'end' => '10:00', 'label' => '9:00 - 10:00'],
            ['start' => '10:00', 'end' => '10:15', 'label' => '10:00 - 10:15', 'break' => true],
            ['start' => '10:15', 'end' => '11:15', 'label' => '10:15 - 11:15'],
            ['start' => '11:15', 'end' => '12:15', 'label' => '11:15 - 12:15'],
            ['start' => '12:15', 'end' => '13:15', 'label' => '12:15 - 1:15', 'break' => true],
            ['start' => '13:15', 'end' => '14:15', 'label' => '1:15 - 2:15'],
            ['start' => '14:15', 'end' => '15:15', 'label' => '2:15 - 3:15'],
            ['start' => '15:15', 'end' => '16:15', 'label' => '3:15 - 4:15'],
        ];
    }

    /**
     * Get standard days of week
     */
    public static function getDaysOfWeek(): array
    {
        return ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    }

    /**
     * Get lab group options
     */
    public static function getLabGroups(): array
    {
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    }

    /**
     * Get section options
     */
    public static function getSections(): array
    {
        return ['A', 'B', 'C', 'D', 'Morning', 'Evening'];
    }
}

