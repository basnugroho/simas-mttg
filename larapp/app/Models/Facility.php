<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['name', 'slug', 'is_required', 'unit_id'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function mosqueFacilities()
    {
        return $this->hasMany(MosqueFacility::class);
    }

    public function mosques()
    {
        return $this->belongsToMany(Mosque::class, 'mosque_facility')
            ->withPivot(['is_available', 'note', 'quantity', 'unit_id'])
            ->withTimestamps();
    }

    public function unit()
    {
        return $this->belongsTo(FacilityUnit::class, 'unit_id');
    }
}
