<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;
use App\Notifications\PasswordResetNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'bio',
        'profile_photo_path',
    ];

    /**
     * Model attribute defaults.
     */
    protected $attributes = [
        'role' => 'admin',
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

    // Relation to student record (if any)
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Relation to teacher record (if any)
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    // Relation to parent record (if any)
    public function parent()
    {
        return $this->hasOne(ParentModel::class);
    }

    /**
     * Get the profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        // First check if profile photo is stored directly on user
        if (!empty($this->profile_photo_path)) {
            return asset('storage/' . $this->profile_photo_path);
        }
        
        // Check related models for profile photo (using optional to avoid errors if not loaded)
        if ($this->role === 'student' && optional($this->student)->profile_photo_path) {
            return asset('storage/' . $this->student->profile_photo_path);
        }
        
        if ($this->role === 'teacher' && optional($this->teacher)->profile_photo_path) {
            return asset('storage/' . $this->teacher->profile_photo_path);
        }
        
        if ($this->role === 'parent' && optional($this->parent)->profile_photo_path) {
            return asset('storage/' . $this->parent->profile_photo_path);
        }
        
        // Fallback to ui-avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=150&background=random';
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Get the appropriate dashboard route based on user role.
     */
    public function getDashboardRoute(): string
    {
        return match($this->role) {
            'admin' => route('admin.dashboard'),
            'teacher' => route('teacher.dashboard'),
            'student' => route('student.dashboard'),
            'parent' => route('parent.dashboard'),
            default => route('dashboard'),
        };
    }
}
