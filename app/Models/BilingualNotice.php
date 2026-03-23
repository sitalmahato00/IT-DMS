<?php

namespace App\Models;

use App\Helpers\NepaliContentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Bilingual Notice Model
 * 
 * Stores notices with both Nepali (Devanagari) and English content.
 * Following Nepal Government Website Standards:
 * - Stores Nepali content directly in Unicode (utf8mb4)
 * - UI labels translated via lang files
 * - Content stored in native script
 * 
 * @property int $id
 * @property string|null $title_ne सूचना शीर्षक (Nepali)
 * @property string|null $title_en Notice Title (English)
 * @property string|null $content_ne सूचना सामग्री (Nepali)
 * @property string|null $content_en Notice Content (English)
 * @property string $audience
 * @property string|null $audience_label_ne दर्शक लेबल (Nepali)
 * @property string $category
 * @property string|null $category_label_ne श्रेणी लेबल (Nepali)
 * @property string $priority
 * @property string|null $priority_label_ne प्राथमिकता लेबल (Nepali)
 * @property \Carbon\Carbon $published_date
 * @property \Carbon\Carbon|null $expiry_date
 * @property string|null $published_date_bs प्रकाशित मिति (Bikram Sambat)
 * @property string|null $expiry_date_bs म्याद समाप्ति मिति (Bikram Sambat)
 * @property bool $is_published
 * @property bool $is_important
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class BilingualNotice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title_ne',
        'title_en',
        'content_ne',
        'content_en',
        'audience',
        'audience_label_ne',
        'category',
        'category_label_ne',
        'priority',
        'priority_label_ne',
        'published_date',
        'expiry_date',
        'published_date_bs',
        'expiry_date_bs',
        'is_published',
        'is_important',
        'is_featured',
        'created_by',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'published_date' => 'date',
        'expiry_date' => 'date',
        'is_published' => 'boolean',
        'is_important' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the localized title based on current locale
     * 
     * @return string|null
     */
    public function getLocalizedTitleAttribute(): ?string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->title_ne,
            $this->title_en
        );
    }

    /**
     * Get the localized content based on current locale
     * 
     * @return string|null
     */
    public function getLocalizedContentAttribute(): ?string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->content_ne,
            $this->content_en
        );
    }

    /**
     * Get the localized priority label
     * 
     * @return string
     */
    public function getPriorityLabelAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ne' && !empty($this->priority_label_ne)) {
            return $this->priority_label_ne;
        }
        
        return match($this->priority) {
            'urgent' => $locale === 'ne' ? 'अत्यावश्यक' : 'Urgent',
            'high' => $locale === 'ne' ? 'महत्वपूर्ण' : 'High',
            'normal' => $locale === 'ne' ? 'सामान्य' : 'Normal',
            'low' => $locale === 'ne' ? 'कम' : 'Low',
            default => '',
        };
    }

    /**
     * Get the localized audience label
     * 
     * @return string
     */
    public function getAudienceLabelAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ne' && !empty($this->audience_label_ne)) {
            return $this->audience_label_ne;
        }
        
        return match($this->audience) {
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'students' => $locale === 'ne' ? 'विद्यार्थीहरू' : 'Students',
            'faculty' => $locale === 'ne' ? 'शिक्षकहरू' : 'Faculty',
            'parents' => $locale === 'ne' ? 'अभिभावकहरू' : 'Parents',
            default => '',
        };
    }

    /**
     * Get formatted published date in current locale
     * 
     * @return string
     */
    public function getFormattedPublishedDateAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ne' && !empty($this->published_date_bs)) {
            return NepaliContentHelper::formatNumber($this->published_date_bs);
        }
        
        return $this->published_date->format('d M Y');
    }

    /**
     * Scope for published notices
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', today());
                    });
    }

    /**
     * Scope for important notices
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true)->published();
    }

    /**
     * Scope by audience
     */
    public function scopeForAudience($query, string $audience)
    {
        return $query->where(function ($q) use ($audience) {
            $q->where('audience', 'all')
              ->orWhere('audience', $audience);
        });
    }

    /**
     * Scope by category
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for current locale
     */
    public function scopeForLocale($query)
    {
        $locale = app()->getLocale();
        
        return $query->where(function ($q) use ($locale) {
            $q->where('locale', 'both')
              ->orWhere('locale', $locale);
        });
    }

    /**
     * Get the user who created this notice
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the category of this notice
     */
    public function category()
    {
        return $this->belongsTo(NoticeCategory::class);
    }
}
