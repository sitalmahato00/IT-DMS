<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'semester',
        'subject_id',
        'category',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the course (subject) for this material.
     */
    public function course()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the user who uploaded this material.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope for notes category.
     */
    public function scopeNotes($query)
    {
        return $query->where('category', 'notes');
    }

    /**
     * Scope for assignments category.
     */
    public function scopeAssignments($query)
    {
        return $query->where('category', 'assignment');
    }

    /**
     * Scope for previous year papers category.
     */
    public function scopePapers($query)
    {
        return $query->where('category', 'paper');
    }

    /**
     * Scope for specific semester.
     */
    public function scopeForSemester($query, $semester)
    {
        if ($semester && $semester !== 'all') {
            return $query->where('semester', $semester);
        }
        return $query;
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) return 'N/A';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension for icon.
     */
    public function getFileExtensionAttribute()
    {
        if (!$this->file_name) return null;
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get category badge class.
     */
    public function getCategoryBadgeClassAttribute()
    {
        return match($this->category) {
            'notes' => 'bg-blue-100 text-blue-700',
            'assignment' => 'bg-green-100 text-green-700',
            'paper' => 'bg-purple-100 text-purple-700',
            'other' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get category display text.
     */
    public function getCategoryTextAttribute()
    {
        return match($this->category) {
            'notes' => 'Notes',
            'assignment' => 'Assignment',
            'paper' => 'Previous Year Paper',
            'other' => 'Other',
            default => 'Material',
        };
    }

    /**
     * Get file icon based on extension.
     */
    public function getFileIconAttribute()
    {
        $ext = $this->file_extension;
        $icons = [
            'pdf' => 'bi-file-earmark-pdf-fill text-red-600',
            'doc' => 'bi-file-earmark-word-fill text-blue-600',
            'docx' => 'bi-file-earmark-word-fill text-blue-600',
            'xls' => 'bi-file-earmark-excel-fill text-green-600',
            'xlsx' => 'bi-file-earmark-excel-fill text-green-600',
            'ppt' => 'bi-file-earmark-ppt-fill text-orange-600',
            'pptx' => 'bi-file-earmark-ppt-fill text-orange-600',
            'jpg' => 'bi-file-earmark-image-fill text-purple-600',
            'jpeg' => 'bi-file-earmark-image-fill text-purple-600',
            'png' => 'bi-file-earmark-image-fill text-purple-600',
            'gif' => 'bi-file-earmark-image-fill text-purple-600',
            'zip' => 'bi-file-earmark-zip-fill text-gray-600',
            'rar' => 'bi-file-earmark-zip-fill text-gray-600',
        ];
        return $icons[$ext] ?? 'bi-file-earmark-fill text-gray-600';
    }

    /**
     * Format semester for display.
     */
    public function getFormattedSemesterAttribute()
    {
        $map = [
            '1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th', '6' => '6th',
        ];
        return $map[$this->semester] ?? $this->semester;
    }
}

