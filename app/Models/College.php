<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class College extends Model
{
    protected $fillable = [
        'name',
        'name_nepali',
        'short_name',
        'logo_path',
        'phone',
        'email',
        'website',
        'address',
        'address_nepali',
        'city',
        'district',
        'province',
        'latitude',
        'longitude',
        'principal_name',
        'principal_phone',
        'principal_email',
        'established_year',
        'registration_number',
        'description',
        'description_nepali',
    ];

    /**
     * Get the logo URL
     */
    public function getLogoUrl()
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return Storage::url($this->logo_path);
        }
        return asset('images/default-logo.svg');
    }
}
