<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'year',
        'month',
        'total_hours',
        'hourly_rate',
        'total_salary',
        'notes',
        'is_paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'total_hours' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'total_salary' => 'decimal:2',
            'is_paid' => 'boolean',
            'paid_at' => 'date',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    public function scopeForYearMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    // ============================================
    // Relationships
    // ============================================
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isPaid(): bool
    {
        return $this->is_paid;
    }

    public function calculateTotalSalary(): float
    {
        return $this->total_hours * $this->hourly_rate;
    }

    public function getMonthName(): string
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return $months[$this->month] ?? '';
    }

    public function getPeriod(): string
    {
        return $this->getMonthName() . ' ' . $this->year;
    }

    // ============================================
    // Boot Method
    // ============================================
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($teacherHour) {
            // Auto-calculate total_salary before saving
            if ($teacherHour->isDirty(['total_hours', 'hourly_rate'])) {
                $teacherHour->total_salary = $teacherHour->calculateTotalSalary();
            }
        });
    }
}
