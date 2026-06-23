<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'start_date',
        'status',
        // Flexible scheduling
        'days_per_week',
        'session_duration',
        'schedule_pattern',
        // Admin pricing
        'admin_price',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'admin_price' => 'decimal:2',
            'days_per_week' => 'integer',
            'session_duration' => 'string',
            'schedule_pattern' => 'array',
        ];
    }

    public function setSessionDurationAttribute($value)
    {
        $this->attributes['session_duration'] = $value !== null ? (string) $value : null;
    }

    // ============================================
    // Scopes
    // ============================================


    // ============================================
    // Relationships
    // ============================================
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function payments()
    {
        return $this->hasMany(EnrollmentPayment::class);
    }

    // ============================================
    // Helper Methods
    // ============================================
    // ============================================
    // Scope Helpers
    // ============================================

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // ============================================
    // Pricing Methods
    // ============================================
    public function getCurrentMonthlyPrice(): ?float
    {
        return $this->admin_price;
    }

    public function getFormattedPrice(): string
    {
        $price = $this->getCurrentMonthlyPrice();
        if (!$price) {
            return 'Not Set';
        }

        $currencySymbols = [
            'CAD' => 'CA$',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'EGP' => 'E£',
        ];

        $symbol = $currencySymbols[$this->currency] ?? '$';
        return $symbol . number_format($price, 2);
    }

    // ============================================
    // Schedule Pattern Methods
    // ============================================
    
    /**
     * Get the schedule pattern for this enrollment
     * Returns array like: ['Monday' => '16:00', 'Wednesday' => '18:00']
     */
    public function getSchedulePattern(): ?array
    {
        return $this->normalizeSchedulePattern($this->schedule_pattern ?? []);
    }

    /**
     * Set the schedule pattern for this enrollment
     * @param array $pattern Array like: ['Monday' => '16:00', 'Wednesday' => '18:00']
     */
    public function setSchedulePattern(array $pattern): void
    {
        $this->schedule_pattern = $this->normalizeSchedulePattern($pattern);
        $this->save();
    }

    /**
     * Check if this enrollment has a schedule pattern
     */
    public function hasSchedulePattern(): bool
    {
        return !empty($this->schedule_pattern);
    }

    /**
     * Get days from the schedule pattern
     * Returns array like: ['Monday', 'Wednesday', 'Friday']
     */
    public function getScheduleDays(): array
    {
        if (!$this->hasSchedulePattern()) {
            return [];
        }
        return array_keys($this->schedule_pattern);
    }

    /**
     * Get time for a specific day from the pattern
     */
    public function getTimeForDay(string $day): ?string
    {
        $pattern = $this->getSchedulePattern() ?? [];
        $times = $pattern[$day] ?? [];
        return is_array($times) ? ($times[0] ?? null) : $times;
    }

    public function getTimesForDay(string $day): array
    {
        $pattern = $this->getSchedulePattern() ?? [];
        $times = $pattern[$day] ?? [];
        if (!is_array($times)) {
            return $times ? [$times] : [];
        }

        return $times;
    }

    protected function normalizeSchedulePattern(array $pattern): array
    {
        $normalized = [];

        foreach ($pattern as $day => $times) {
            if (is_string($times)) {
                $times = [$times];
            }

            if (!is_array($times)) {
                continue;
            }

            $cleanTimes = array_values(array_filter(array_map(function ($time) {
                return is_string($time) ? trim($time) : null;
            }, $times)));

            if (!empty($cleanTimes)) {
                $normalized[$day] = array_values(array_unique($cleanTimes));
            }
        }

        return $normalized;
    }
}
