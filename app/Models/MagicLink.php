<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Importing User model

class MagicLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'used_at',
        'ip',
        'user_agent',
    ];

    protected $dates = [
        'expires_at',
        'used_at',
    ];

    /**
     * Ensure datetime casting for modern Laravel versions.
     * This guarantees `expires_at` and `used_at` are Carbon instances
     * even when config caching or older $dates behavior varies on deploy.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
