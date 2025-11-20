<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mosque extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'address',
        'province_id', 'city_id', 'witel_id',
        'regional_id', 'area_id', 'sto_id',
        'tahun_didirikan', 'jml_bkm', 'luas_tanah', 'daya_tampung',
        'latitude', 'longitude', 'image_url',
        'description', 'completion_percentage',
        // new fields
        'witel_new', 'subsidiary_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function province()
    {
        return $this->belongsTo(Regions::class, 'province_id');
    }

    public function regional()
    {
        return $this->belongsTo(Regions::class, 'regional_id');
    }

    public function area()
    {
        return $this->belongsTo(Regions::class, 'area_id');
    }

    public function city()
    {
        return $this->belongsTo(Regions::class, 'city_id');
    }

    public function witel()
    {
        return $this->belongsTo(Regions::class, 'witel_id');
    }

    public function sto()
    {
        return $this->belongsTo(Regions::class, 'sto_id');
    }

    public function mosqueFacility()
    {
        return $this->hasMany(MosqueFacility::class);
    }

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class, 'subsidiary_id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_mosque')
            ->withPivot(['note'])
            ->withTimestamps();
    }

    public function facility()
    {
        return $this->belongsToMany(Facility::class, 'mosque_facility')
            ->withPivot(['is_available', 'note'])
            ->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany(MosquePhoto::class)->orderBy('sort_order')->orderBy('id');
    }
}
