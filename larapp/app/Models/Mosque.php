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
        'bank_name', 'bank_account_name', 'bank_account_number',
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

    /**
     * Legacy single subsidiary relation (for backward compatibility)
     */
    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class, 'subsidiary_id');
    }

    /**
     * Many-to-many subsidiaries relationship
     */
    public function subsidiaries()
    {
        return $this->belongsToMany(Subsidiary::class, 'mosque_subsidiary')
            ->withTimestamps();
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_mosque')
            ->withPivot(['note', 'event_start', 'event_end'])
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

    public function managers()
    {
        return $this->hasMany(MosqueManager::class);
    }

    public function cashPositions()
    {
        return $this->hasMany(CashPosition::class);
    }

    /**
     * Return a human-friendly region path for this mosque.
     * Order: regional -> area -> witel -> sto -> province -> city
     */
    public function regionPath(): string
    {
        $parts = [];
        try {
            if ($this->regional?->name) $parts[] = $this->regional->name;
            if ($this->area?->name) $parts[] = $this->area->name;
            if ($this->witel?->name) $parts[] = $this->witel->name;
            if ($this->sto?->name) $parts[] = $this->sto->name;
            if ($this->province?->name) $parts[] = $this->province->name;
            if ($this->city?->name) $parts[] = $this->city->name;
        } catch (\Throwable $e) {
            // fallback: try to return any available raw columns
            $raw = [];
            foreach (['regional_id','area_id','witel_id','sto_id','province_id','city_id'] as $c) {
                if (isset($this->$c) && $this->$c) $raw[] = (string)$this->$c;
            }
            return count($raw) ? implode(' → ', $raw) : '-';
        }
        return count($parts) ? implode(' → ', $parts) : '-';
    }

    public function getPublicImageUrlAttribute()
    {
        $v = $this->image_url ?? null;
        if (empty($v)) {
            return asset('images/mosque-1.jpg');
        }
        if (preg_match('/^https?:\/\//', $v)) {
            return $v;
        }
        if (strpos($v, 'storage/') === 0) {
            return asset($v);
        }
        try {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($v);
        } catch (\Exception $e) {
            return asset('images/mosque-1.jpg');
        }
    }

    /**
     * Return an URL derived only from the raw DB value (`image_url`) without
     * invoking Storage::disk. This uses the DB value as-is and applies a
     * minimal normalization so it becomes a usable URL in templates.
     *
     * Examples:
     * - absolute URL (http://...) -> returned as-is
     * - starts with '/' -> returned as-is
     * - starts with 'storage/' -> returned as asset('storage/...')
     * - otherwise -> prefixed with '/storage/' so it points to public storage
     */
    public function getDbImageUrlAttribute()
    {
        // If photos relation is loaded and has a path, prefer that as the DB image
        if ($this->relationLoaded('photos') && $this->photos->count()) {
            $first = $this->photos->first();
            if (!empty($first->path)) {
                try {
                    return \Illuminate\Support\Facades\Storage::url($first->path);
                } catch (\Exception $e) {
                    // fall through to image_url handling
                }
            }
        }

        $v = $this->image_url ?? null;
        if (empty($v)) return null;
        if (preg_match('/^https?:\/\//', $v)) return $v;
        if (strpos($v, '/') === 0) return $v; // already absolute path
        if (strpos($v, 'storage/') === 0) return asset($v);
        // treat as a storage relative path (db stores 'mosques/30/..')
        return asset('storage/' . ltrim($v, '/'));
    }
}
