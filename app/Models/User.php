<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;
use App\Notifications\PasswordResetNotification;
use App\Support\Media;

class User extends Authenticatable implements MustVerifyEmail
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
        'username',
        'password',
        'phone',
        'department',
        'bio',
        'profile_photo_path',
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
    public function getProfilePhotoUrlAttribute(): ?string
    {
        // First check if profile photo is stored directly on user
        if (!empty($this->profile_photo_path)) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_photo_path)) {
                return \Illuminate\Support\Facades\Storage::url($this->profile_photo_path);
            }
            return null;
        }
        
        // Check related models for profile photo (using optional to avoid errors if not loaded)
        if ($this->role === 'student' && optional($this->student)->profile_photo_path) {
            $path = $this->student->profile_photo_path;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return \Illuminate\Support\Facades\Storage::url($path);
            }
        }
        
        if ($this->role === 'teacher' && optional($this->teacher)->profile_photo_path) {
            $path = $this->teacher->profile_photo_path;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return \Illuminate\Support\Facades\Storage::url($path);
            }
        }
        
        if ($this->role === 'parent' && optional($this->parent)->profile_photo_path) {
            $path = $this->parent->profile_photo_path;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return \Illuminate\Support\Facades\Storage::url($path);
            }
        }
        
        return asset('images/default-logo.svg');
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
