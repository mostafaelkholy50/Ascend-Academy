<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\RescheduleRequestStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RescheduleRequest extends Model
{
    protected $fillable = [
        'schedule_id',
        'teacher_id',
        'student_id',
        'new_starts_at',
        'new_ends_at',
        'status',
    ];

    protected $casts = [
        'new_starts_at' => 'datetime',
        'new_ends_at' => 'datetime',
        'status' => RescheduleRequestStatus::class,
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
