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
     * Returns array like: ['Monday' => ['active' => true, 'slots' => [['time' => '16:00', 'duration' => 60]]]]
     */
    public function getSchedulePattern(): ?array
    {
        return $this->normalizeSchedulePattern($this->schedule_pattern ?? []);
    }

    /**
     * Set the schedule pattern for this enrollment
     * @param array $pattern Schedule pattern data
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

    public function isDayScheduleActive(string $day): bool
    {
        $pattern = $this->getSchedulePattern() ?? [];
        return !empty($pattern[$day]['active']);
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
        return array_keys(array_filter($this->getSchedulePattern() ?? [], fn ($dayData) => !empty($dayData['active'])));
    }

    /**
     * Get time for a specific day from the pattern
     */
    public function getTimeForDay(string $day): ?string
    {
        $pattern = $this->getSchedulePattern() ?? [];
        $times = $pattern[$day]['slots'] ?? [];
        return is_array($times) ? ($times[0] ?? null) : $times;
    }

    public function getTimesForDay(string $day): array
    {
        $pattern = $this->getSchedulePattern() ?? [];
        $times = $pattern[$day]['slots'] ?? [];
        if (!is_array($times)) {
            return $times ? [$times] : [];
        }

        return $times;
    }

    protected function normalizeSchedulePattern(array $pattern): array
    {
        $normalized = [];

        foreach ($pattern as $day => $items) {
            $active = true;
            $slots = [];

            if (is_array($items) && array_key_exists('slots', $items)) {
                $active = isset($items['active']) ? (bool) $items['active'] : true;
                $items = $items['slots'] ?? [];
            } elseif (is_array($items) && array_key_exists('active', $items)) {
                $active = (bool) $items['active'];
                $items = [];
            }

            if (is_string($items)) {
                $items = [['time' => $items, 'duration' => (int)($this->session_duration ?? 60)]];
            }

            if (!is_array($items)) {
                continue;
            }

            $cleanItems = [];
            foreach ($items as $item) {
                if (is_string($item)) {
                    $cleanItems[] = ['time' => trim($item), 'duration' => (int)($this->session_duration ?? 60)];
                } elseif (is_array($item) && isset($item['time'])) {
                    $cleanItems[] = [
                        'time' => trim($item['time']), 
                        'duration' => isset($item['duration']) ? (int)$item['duration'] : (int)($this->session_duration ?? 60)
                    ];
                }
            }

            // Remove duplicates based on time
            $uniqueTimes = [];
            $finalItems = [];
            foreach ($cleanItems as $item) {
                if (!in_array($item['time'], $uniqueTimes)) {
                    $uniqueTimes[] = $item['time'];
                    $finalItems[] = $item;
                }
            }

            if (!empty($finalItems)) {
                $normalized[$day] = [
                    'active' => $active,
                    'slots' => $finalItems,
                ];
            }
        }

        return $normalized;
    }
}
