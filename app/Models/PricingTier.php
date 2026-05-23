<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'days_per_week',
        'session_duration',
        'price_cad',
        'price_usd',
        'price_gbp',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'days_per_week' => 'integer',
            'session_duration' => 'string',
            'price_cad' => 'decimal:2',
            'price_usd' => 'decimal:2',
            'price_gbp' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function setSessionDurationAttribute($value)
    {
        $this->attributes['session_duration'] = $value !== null ? (string) $value : null;
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchedule($query, $daysPerWeek, $sessionDuration)
    {
        return $query->where('days_per_week', $daysPerWeek)
            ->where('session_duration', $sessionDuration);
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function getPriceForCurrency($currency)
    {
        return match(strtoupper($currency)) {
            'CAD' => $this->price_cad,
            'USD' => $this->price_usd,
            'GBP' => $this->price_gbp,
            default => $this->price_cad,
        };
    }

    public function getFormattedPrice($currency)
    {
        $price = $this->getPriceForCurrency($currency);
        
        $symbols = [
            'CAD' => 'CA$',
            'USD' => '$',
            'GBP' => '£',
        ];

        $symbol = $symbols[strtoupper($currency)] ?? '$';
        return $symbol . number_format($price, 2);
    }

    public function getScheduleDescription()
    {
        $days = $this->days_per_week == 1 ? '1 day' : "{$this->days_per_week} days";
        $duration = $this->session_duration == 30 ? '30 min' : '1 hour';
        return "{$days}/week × {$duration}";
    }

    // Static method to get suggested price for enrollment
    public static function getSuggestedPrice($daysPerWeek, $sessionDuration, $currency = 'CAD')
    {
        $tier = self::active()
            ->forSchedule($daysPerWeek, $sessionDuration)
            ->first();

        return $tier ? $tier->getPriceForCurrency($currency) : null;
    }
}
