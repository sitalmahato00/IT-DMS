<?php

namespace App\Models;

use App\Helpers\NepaliContentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ne',
        'message',
        'message_ne',
        'audience',
        'audience_ne',
        'status',
        'semester',
        'subject_id',
        'is_important',
        'published_at',
        'published_at_bs',
        'created_by',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'is_important' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the localized title based on current locale
     */
    public function getLocalizedTitleAttribute(): string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->title_ne,
            $this->title
        ) ?? $this->title ?? '';
    }

    /**
     * Get the localized message based on current locale
     */
    public function getLocalizedMessageAttribute(): string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->message_ne,
            $this->message
        ) ?? $this->message ?? '';
    }

    /**
     * Get the localized audience label based on current locale
     */
    public function getLocalizedAudienceLabelAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ne' && !empty($this->audience_ne)) {
            return $this->audience_ne;
        }
        
        return match($this->audience) {
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'students' => $locale === 'ne' ? 'विद्यार्थीहरू' : 'Students',
            'faculty' => $locale === 'ne' ? 'शिक्षकहरू' : 'Faculty',
            'parents' => $locale === 'ne' ? 'अभिभावकहरू' : 'Parents',
            default => $locale === 'ne' ? 'सबै' : 'All',
        };
    }

    /**
     * Get the localized priority label based on current locale
     */
    public function getLocalizedPriorityLabelAttribute(): string
    {
        if ($this->is_important) {
            return app()->getLocale() === 'ne' ? 'महत्वपूर्ण' : 'Important';
        }
        return app()->getLocale() === 'ne' ? 'सामान्य' : 'Normal';
    }

    /**
     * Get formatted date in current locale
     */
    public function getFormattedDateAttribute(): string
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ne' && !empty($this->published_at_bs)) {
            return NepaliContentHelper::formatNumber($this->published_at_bs);
        }
        
        return $this->published_at 
            ? $this->published_at->format('d M Y') 
            : $this->created_at->format('d M Y');
    }

    /**
     * Get formatted semester in current locale
     */
    public function getFormattedSemesterAttribute(): string
    {
        if (!$this->semester) return '';
        
        $locale = app()->getLocale();
        $semesterMap = [
            '1' => ['ne' => 'प्रथम', 'en' => '1st'],
            '2' => ['ne' => 'द्वितीय', 'en' => '2nd'],
            '3' => ['ne' => 'तृतीय', 'en' => '3rd'],
            '4' => ['ne' => 'चतुर्थ', 'en' => '4th'],
            '5' => ['ne' => 'पंचम', 'en' => '5th'],
            '6' => ['ne' => 'षष्ठ', 'en' => '6th'],
        ];
        
        return $semesterMap[$this->semester][$locale] ?? $this->semester;
    }

    /**
     * Get the user who created this notice
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the subject for this notice
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Scope for published notices
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for draft notices
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for scheduled notices
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for important notices
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope for specific audience
     */
    public function scopeForAudience($query, $audience)
    {
        if ($audience && $audience !== 'all') {
            if ($audience === 'teacher') {
                $audience = 'faculty';
            }
            return $query->where(function($q) use ($audience) {
                $q->where('audience', $audience)
                  ->orWhere('audience', 'all');
            });
        }
        return $query;
    }

    /**
     * Scope for specific semester
     */
    public function scopeForSemester($query, $semester)
    {
        if ($semester && $semester !== 'all') {
            $map = [
                'first' => '1', 'second' => '2', 'third' => '3', 'fourth' => '4', 'fifth' => '5', 'sixth' => '6',
                '1st' => '1', '2nd' => '2', '3rd' => '3', '4th' => '4', '5th' => '5', '6th' => '6',
            ];

            $candidates = [];
            $candidates[] = $semester;
            $candidates[] = ucfirst($semester);
            $candidates[] = strtoupper($semester);
            $lower = strtolower($semester);
            if (isset($map[$lower])) {
                $candidates[] = $map[$lower];
            }

            $candidates = array_values(array_unique(array_filter($candidates)));

            return $query->where(function($q) use ($candidates) {
                foreach ($candidates as $c) {
                    $q->orWhere('semester', $c);
                }
            });
        }
        return $query;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'published' => 'bg-green-100 text-green-700',
            'draft' => 'bg-orange-100 text-orange-700',
            'scheduled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get audience display text (legacy - use localized_audience_label)
     */
    public function getAudienceTextAttribute()
    {
        return match($this->audience) {
            'all' => 'All',
            'students' => 'Students',
            'faculty' => 'Faculty',
            'parents' => 'Parents',
            default => 'All',
        };
    }

    /**
     * Get color for notice indicator
     */
    public function getIndicatorColorAttribute()
    {
        if ($this->is_important) {
            return 'bg-red-500';
        }
        return match($this->audience) {
            'students' => 'bg-blue-500',
            'faculty' => 'bg-purple-500',
            'parents' => 'bg-green-500',
            default => 'bg-yellow-500',
        };
    }

    /**
     * Check if notice has an attachment
     */
    public function getHasAttachmentAttribute()
    {
        return !empty($this->file_path);
    }

    /**
     * Get the file extension
     */
    public function getFileExtensionAttribute()
    {
        if (!$this->file_name) return null;
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeAttribute()
    {
        if (!$this->file_path) return null;
        
        $size = filesize(storage_path('app/' . $this->file_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}

