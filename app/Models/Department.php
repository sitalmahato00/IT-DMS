<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Support\Media;

class Department extends Model
{
    protected $fillable = [
        'name',
        'name_nepali',
        'short_name',
        'logo_path',
        'hero_images',
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
        'map_embed_url',
        'map_label',
        'principal_name',
        'principal_phone',
        'principal_email',
        'established_year',
        'registration_number',
        'description',
        'description_nepali',
        'programs_title',
        'programs_title_nepali',
        'programs_content',
        'programs_content_nepali',
        'programs_image_path',
    ];

    protected $casts = [
        'hero_images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function getTable()
    {
        try {
            if (Schema::hasTable('departments')) {
                return 'departments';
            }

            // Backward-compat: before the DB is reset/renamed, the table may still be "colleges".
            if (Schema::hasTable('colleges')) {
                return 'colleges';
            }
        } catch (\Throwable $e) {
            // Ignore and fall back to the default below.
        }

        return parent::getTable();
    }

    public function getLogoUrl()
    {
        return $this->logo_url;
    }

    public function getLogoUrlAttribute(): string
    {
        return Media::publicUrl($this->logo_path, '/images/default-logo.svg');
    }

    public function getProgramsImageUrl(): ?string
    {
        return $this->programs_image_url;
    }

    public function getProgramsImageUrlAttribute(): ?string
    {
        return Media::publicUrl($this->programs_image_path);
    }
}
