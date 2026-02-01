<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'audience',
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
            // accept 'teacher' as an alias for 'faculty'
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
            // Build candidate semester formats to match stored values (numeric, textual, ordinal)
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
     * Get audience display text
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
     * Format date for display
     */
    public function getFormattedDateAttribute()
    {
        return $this->published_at_bs ? $this->published_at_bs : ($this->published_at ? $this->published_at->format('M d, Y') : $this->created_at->format('M d, Y'));
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

