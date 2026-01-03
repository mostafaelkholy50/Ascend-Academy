<?php
// app/Models/Children.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Children extends Model
{
    use HasFactory;

    // ✅ تحديد اسم الجدول بشكل صريح
    protected $table = 'children';

    protected $fillable = [
        'parent_id',
        'child_id',
    ];

    // ============================================
    // Relationships
    // ============================================
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'child_id');
    }
}
