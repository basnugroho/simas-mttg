<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mosque extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'address',
        'province_id', 'city_id', 'witel_id',
        'latitude', 'longitude', 'image_url',
        'description', 'completion_percentage',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function province()
    {
        return $this->belongsTo(Regions::class, 'province_id');
    }

    public function city()
    {
        return $this->belongsTo(Regions::class, 'city_id');
    }

    public function witel()
    {
        return $this->belongsTo(Regions::class, 'witel_id');
    }

    public function mosqueFacility()
    {
        return $this->hasMany(MosqueFacility::class);
    }

    public function facility()
    {
        return $this->belongsToMany(Facility::class, 'mosque_facility')
            ->withPivot(['is_available', 'note'])
            ->withTimestamps();
    }
}
