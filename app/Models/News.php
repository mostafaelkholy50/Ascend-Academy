<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];

    // Automatically generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = \Str::slug($news->title);
            }
            if (empty($news->published_at) && $news->is_published) {
                $news->published_at = now();
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = \Str::slug($news->title);
            }
            if ($news->isDirty('is_published') && $news->is_published && empty($news->published_at)) {
                $news->published_at = now();
            }
        });
    }

    // Scope for published news
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    // Get excerpt from description
    public function getExcerpt($length = 150)
    {
        return \Str::limit(strip_tags($this->description), $length);
    }
    
}
