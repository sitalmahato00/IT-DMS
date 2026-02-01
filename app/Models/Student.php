<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roll_no',
        'semester',
        'department',
        'parent_id',
        'date_of_birth',
        'date_of_birth_bs',
        'address',
        'batch_year',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
