<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subsidiary extends Model
{
    protected $fillable = [
        'name', 'slug', 'created_by',
    ];

    public function mosques()
    {
        return $this->hasMany(Mosque::class, 'subsidiary_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
