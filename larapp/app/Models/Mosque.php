<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mosque extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'address',
        'province', 'city',
        'province_id', 'city_id', 'witel_id',
        'regional_id', 'area_id', 'sto_id',
        'tahun_didirikan', 'jml_bkm', 'luas_tanah', 'daya_tampung',
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

    /**
     * Recompute and persist completion percentage based on required facilities.
     * completion = floor( (count of required facilities owned by this mosque) / (total required facilities) * 100 )
     */
    public function recomputeCompletionPercentage()
    {
        $totalRequired = Facility::where('is_required', true)->count();
        if ($totalRequired <= 0) {
            $this->completion_percentage = 0;
            $this->saveQuietly();
            return $this->completion_percentage;
        }

        $ownedRequired = MosqueFacility::where('mosque_id', $this->id)
            ->whereHas('facility', function ($q) {
                $q->where('is_required', true);
            })
            ->where('is_available', true)
            ->count();

        $pct = (int) floor(($ownedRequired / $totalRequired) * 100);
        $pct = max(0, min(100, $pct));
        $this->completion_percentage = $pct;
        $this->saveQuietly();
        return $this->completion_percentage;
    }
}
