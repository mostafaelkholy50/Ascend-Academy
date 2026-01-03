<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'photo',
        'level',
        'age_group',
        'language',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
        ];
    }

    // ============================================
    // Query Scopes
    // ============================================
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByAgeGroup($query, $ageGroup)
    {
        return $query->where('age_group', $ageGroup);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // ============================================
    // Helper Methods
    // ============================================
    public function getPhotoUrl()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('assets/images/16f75e5576780b777d75ebcbad5f47edd97ef4cb.jpg');
    }

    // ============================================
    // Relationships
    // ============================================
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->withPivot(['start_date', 'end_date', 'status'])
            ->withTimestamps();
    }
}
