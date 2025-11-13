<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MosqueFacility extends Model
{
    protected $table = 'mosque_facility';

    protected $fillable = [
        'mosque_id', 'facility_id', 'is_available', 'note',
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
}
