<?php

namespace App\Models;

use App\Helpers\NepaliContentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    use HasFactory;

    protected $table = 'study_materials';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'document_type',
        'title',
        'title_ne',
        'file_name',
        'file_path',
        'file_size',
        'description',
        'description_ne',
        'semester',
        'visibility',
        'is_published',
        'uploaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_published' => 'boolean',
        'uploaded_at' => 'datetime',
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
     * Get the localized description based on current locale
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        return NepaliContentHelper::getLocalizedContent(
            $this->description_ne,
            $this->description
        ) ?? $this->description ?? '';
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
     * Get document type label in current locale
     */
    public function getLocalizedDocumentTypeLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $typeMap = [
            'lecture_notes' => ['ne' => 'लेक्चर नोट्स', 'en' => 'Lecture Notes'],
            'assignment' => ['ne' => 'एसाइनमेंट', 'en' => 'Assignment'],
            'lab_report' => ['ne' => 'ल्याब रिपोर्ट', 'en' => 'Lab Report'],
            'assessment' => ['ne' => 'मूल्यांकन', 'en' => 'Assessment'],
            'study_guide' => ['ne' => 'अध्ययन गाइड', 'en' => 'Study Guide'],
            'syllabus' => ['ne' => 'पाठ्यक्रम', 'en' => 'Syllabus'],
            'project_material' => ['ne' => 'प्रोजेक्ट सामग्री', 'en' => 'Project Material'],
        ];
        
        return $typeMap[$this->document_type][$locale] ?? $this->document_type;
    }

    /**
     * Get visibility label in current locale
     */
    public function getLocalizedVisibilityLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return match($this->visibility) {
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'students' => $locale === 'ne' ? 'विद्यार्थीहरू' : 'Students',
            'faculty' => $locale === 'ne' ? 'शिक्षकहरू' : 'Faculty',
            default => $locale === 'ne' ? 'सबै' : 'All',
        };
    }

    /**
     * Get the subject for this material.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the user who uploaded this material.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Backward-compatible alias for the uploader relationship.
     */
    public function uploader()
    {
        return $this->teacher();
    }

    /**
     * Scope for lecture notes.
     */
    public function scopeLectureNotes($query)
    {
        return $query->where('document_type', 'lecture_notes');
    }

    /**
     * Scope for assignments.
     */
    public function scopeAssignments($query)
    {
        return $query->where('document_type', 'assignment');
    }

    /**
     * Scope for assessments (papers).
     */
    public function scopeAssessments($query)
    {
        return $query->where('document_type', 'assessment');
    }

    /**
     * Scope for lab reports.
     */
    public function scopeLabReports($query)
    {
        return $query->where('document_type', 'lab_report');
    }

    /**
     * Scope for published materials.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for ordering by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
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
     * Get document type badge class.
     */
    public function getDocumentTypeBadgeClassAttribute()
    {
        return match($this->document_type) {
            'lecture_notes' => 'bg-blue-100 text-blue-700',
            'assignment' => 'bg-green-100 text-green-700',
            'lab_report' => 'bg-purple-100 text-purple-700',
            'assessment' => 'bg-orange-100 text-orange-700',
            'study_guide' => 'bg-cyan-100 text-cyan-700',
            'syllabus' => 'bg-pink-100 text-pink-700',
            'project_material' => 'bg-indigo-100 text-indigo-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get document type display text (legacy - use localized_document_type_label).
     */
    public function getDocumentTypeTextAttribute()
    {
        return match($this->document_type) {
            'lecture_notes' => 'Lecture Notes',
            'assignment' => 'Assignment',
            'lab_report' => 'Lab Report',
            'assessment' => 'Assessment/Paper',
            'study_guide' => 'Study Guide',
            'syllabus' => 'Syllabus',
            'project_material' => 'Project Material',
            default => 'Material',
        };
    }

    /**
     * Get category display text (alias for document_type).
     */
    public function getCategoryTextAttribute()
    {
        return $this->document_type_text;
    }

    /**
     * Get category badge class (alias for document_type).
     */
    public function getCategoryBadgeClassAttribute()
    {
        return $this->document_type_badge_class;
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
     * Backward compatibility accessor for category.
     */
    public function getCategoryAttribute()
    {
        return $this->document_type;
    }

    /**
     * Backward compatibility accessor for uploaded_by.
     */
    public function getUploadedByAttribute()
    {
        return $this->teacher_id;
    }
}
