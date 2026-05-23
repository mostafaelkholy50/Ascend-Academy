<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'course_id',
        'title',
        'description',
        'type',
        'file_path',
        'mime_type',
        'external_url',
    ];

    // ============================================
    // Scopes
    // ============================================
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeGeneral($query)
    {
        return $query->whereNull('student_id');
    }

    // ============================================
    // Relationships
    // ============================================
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isFile(): bool
    {
        return !empty($this->file_path);
    }

    public function isLink(): bool
    {
        return $this->type === 'link' && !empty($this->external_url);
    }

    public function getUrl(): ?string
    {
        if ($this->isLink()) {
            return $this->external_url;
        }

        // For files on private storage, we return a secure download route instead of a direct URL
        if ($this->isFile()) {
            return route('teacher.resources.download', $this->id);
        }

        return null;
    }

    public function getFileSize(): ?int
    {
        if ($this->isFile() && Storage::disk('local')->exists($this->file_path)) {
            return Storage::disk('local')->size($this->file_path);
        }

        return null;
    }

    public function deleteFile(): bool
    {
        if ($this->isFile() && Storage::disk('local')->exists($this->file_path)) {
            return Storage::disk('local')->delete($this->file_path);
        }

        return false;
    }
}
