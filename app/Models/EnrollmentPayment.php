<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnrollmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'month',
        'amount',
        'currency',
        'payment_status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // ============================================
    // Relationships
    // ============================================
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    public function scopeForMonth($query, $month)
    {
        return $query->whereMonth('month', $month);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('month', $year);
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function isPartial(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function getFormattedAmount(): string
    {
        $currencySymbols = [
            'CAD' => 'CA$',
            'USD' => '$',
            'GBP' => '£',
        ];

        $symbol = $currencySymbols[$this->currency] ?? '$';
        return $symbol . number_format($this->amount, 2);
    }

    public function getMonthName(): string
    {
        return $this->month->format('F Y');
    }
}
