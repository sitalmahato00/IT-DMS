<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'profile_photo_path',
        'department',
        'bio',
        'status',
        'is_alumni',
    ];

    protected $casts = [
        'is_alumni' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
