<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $fillable = [
        'name', 'type', 'code', 'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function mosques()
    {
        return $this->hasMany(Mosque::class, 'province_id'); // atau city_id/witel_id tergantung kebutuhan
    }
}
