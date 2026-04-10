<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableGapOverride extends Model
{
    use HasFactory;

    protected $table = 'timetable_gap_overrides';

    protected $fillable = [
        'semester',
        'section',
        'day_of_week',
        'start_time',
        'end_time',
    ];
}

