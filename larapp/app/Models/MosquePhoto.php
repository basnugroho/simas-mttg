<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MosquePhoto extends Model
{
    protected $table = 'mosque_photos';

    protected $fillable = [
        'mosque_id', 'path', 'caption', 'sort_order'
    ];

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }
}
