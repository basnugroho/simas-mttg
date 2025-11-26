<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'category_id', 'summary', 'content',
        'image_url', 'published_at', 'status', 'mosque_id', 'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }
}
