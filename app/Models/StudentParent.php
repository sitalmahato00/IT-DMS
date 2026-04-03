<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Media;

class StudentParent extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'parent_code',
        'occupation',
        'national_id_number',
        'date_of_birth',
        'relationship',
        'phone',
        'secondary_phone',
        'alternate_email',
        'whatsapp_number',
        'preferred_contact_method',
        'address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'employer_name',
        'work_address',
        'work_phone_number',
        'income_range',
        'blood_group',
        'medical_conditions',
        'emergency_notes',
        'profile_photo_path',
        'id_proof_path',
        'address_proof_path',
        'department',
        'bio',
        'status',
        'notification_preferences',
        'access_level',
        'portal_access',
        'notes',
        'preferred_language',
        'profile_visibility',
        'emergency_contact_priority',
        'primary_child_user_id',
        'gender',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_of_birth' => 'date',
        'portal_access' => 'boolean',
        'emergency_contact_priority' => 'boolean',
        'primary_child_user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return Media::publicUrl($this->profile_photo_path);
    }

    public function getIdProofUrlAttribute(): ?string
    {
        return Media::publicUrl($this->id_proof_path);
    }

    public function getAddressProofUrlAttribute(): ?string
    {
        return Media::publicUrl($this->address_proof_path);
    }
}
