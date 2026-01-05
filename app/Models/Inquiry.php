<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'full_name',
        'email',
        'phone',
        'child_name',
        'child_age',
        'child_gender',
        'country',
        'city',
        'preferred_course',
        'message',
        'status',
        'admin_notes',
        // New Fields
        'join_date',
        'age',
        'study_hours',
        'courses_needed',
        'sessions_per_week',
        'available_days',
        'referrer',
        'gender',
        'city_state',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'join_date' => 'date',
            'available_days' => 'array',
        ];
    }

    // ============================================
    // Scopes - Filter by Type
    // ============================================
    public function scopeTrials($query)
    {
        return $query->where('type', 'trial');
    }

    public function scopeContacts($query)
    {
        return $query->where('type', 'contact');
    }

    public function scopeRegistrations($query)
    {
        return $query->where('type', 'registration');
    }

    // ============================================
    // Scopes - Filter by Status
    // ============================================
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isContacted(): bool
    {
        return $this->status === 'contacted';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function isTrial(): bool
    {
        return $this->type === 'trial';
    }

    public function isContact(): bool
    {
        return $this->type === 'contact';
    }

    public function isRegistration(): bool
    {
        return $this->type === 'registration';
    }

    public function markAsContacted(): void
    {
        $this->update(['status' => 'contacted']);
    }

    public function markAsConverted(): void
    {
        $this->update(['status' => 'converted']);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    // ============================================
    // Accessors
    // ============================================
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'trial' => 'Free Trial Request',
            'contact' => 'Contact Message',
            'registration' => 'Registration Interest',
            default => 'Unknown',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'contacted' => 'Contacted',
            'converted' => 'Converted',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'contacted' => 'blue',
            'converted' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
