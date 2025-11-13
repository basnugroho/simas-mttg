<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerTimes extends Model
{
    protected $fillable = [
        'region_id', 'date', 'subuh', 'dzuhur', 'ashar', 'maghrib', 'isya',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
