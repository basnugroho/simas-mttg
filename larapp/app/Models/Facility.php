<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['name', 'slug', 'is_required'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function mosqueFacilities()
    {
        return $this->hasMany(MosqueFacility::class);
    }

    public function mosques()
    {
        return $this->belongsToMany(Mosque::class, 'mosque_facilities')
            ->withPivot(['is_available', 'note'])
            ->withTimestamps();
    }
}
