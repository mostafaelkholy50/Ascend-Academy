<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'country',
        'city',
        'gender',
        'birth_date',
        'education_level',
        'certifications',
        'years_of_experience',
        'teaching_experience',
        'subjects',
        'age_groups',
        'teaching_methodology',
        'availability',
        'has_stable_internet',
        'has_quiet_space',
        'why_join',
        'cv_path',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'subjects' => 'array',
            'age_groups' => 'array',
            'availability' => 'array',
            'has_stable_internet' => 'boolean',
            'has_quiet_space' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // ============================================
    // Scopes
    // ============================================
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'reviewed' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'converted' => 'Converted to Teacher',
            default => 'Unknown',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'converted' => 'purple',
            default => 'gray',
        };
    }
}
