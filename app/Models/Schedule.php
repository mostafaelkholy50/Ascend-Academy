<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'course_id',
        'teacher_id',
        'student_id',
        'starts_at',
        'ends_at',
        'zoom_link',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('starts_at', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())
            ->orderBy('starts_at', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now())
            ->orderBy('starts_at', 'desc');
    }

    public function scopeForBillingCycle($query, $start, $end)
    {
        return $query->whereBetween('starts_at', [$start, $end]);
    }

    // ============================================
    // Relationships
    // ============================================
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getDurationInHours(): float
    {
        if (!$this->ends_at) {
            return 0;
        }
        return $this->starts_at->diffInMinutes($this->ends_at) / 60;
    }

    public function getDurationInMinutes(): int
    {
        if (!$this->ends_at) {
            return 0;
        }
        return $this->starts_at->diffInMinutes($this->ends_at);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // ============================================
    // Conflict Detection
    // ============================================
    public static function hasTeacherConflict($teacherId, $startsAt, $endsAt, $excludeId = null)
    {
        return self::where('teacher_id', $teacherId)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt])
                  ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                  ->orWhere(function($q2) use ($startsAt, $endsAt) {
                      $q2->where('starts_at', '<=', $startsAt)
                         ->where('ends_at', '>=', $endsAt);
                  });
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public static function hasStudentConflict($studentId, $startsAt, $endsAt, $excludeId = null)
    {
        return self::where('student_id', $studentId)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt])
                  ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                  ->orWhere(function($q2) use ($startsAt, $endsAt) {
                      $q2->where('starts_at', '<=', $startsAt)
                         ->where('ends_at', '>=', $endsAt);
                  });
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }
}
