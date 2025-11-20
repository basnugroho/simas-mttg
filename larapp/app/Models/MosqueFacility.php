<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MosqueFacility extends Model
{
    protected $table = 'mosque_facility';

    protected $fillable = [
        'mosque_id', 'facility_id', 'is_available', 'note', 'quantity', 'unit_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function unit()
    {
        return $this->belongsTo(FacilityUnit::class, 'unit_id');
    }

    public function photos()
    {
        return $this->hasMany(MosqueFacilityPhoto::class)->orderBy('sort_order')->orderBy('id');
    }
}
