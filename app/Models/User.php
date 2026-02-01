<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Student;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'teacher_id',
        'phone',
        'department',
        'bio',
        'profile_photo_path',
        'role',
        'status',
    ];

    /**
     * Model attribute defaults.
     */
    protected $attributes = [
        'role' => 'student',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Accessor for profile photo full URL
    public function getProfilePhotoUrlAttribute()
    {
        if (!empty($this->profile_photo_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->profile_photo_path);
        }

        return null;
    }

    // Relation to student record (if any)
    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
