<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MosqueFacilityPhoto extends Model
{
    protected $fillable = ['mosque_facility_id', 'path', 'caption', 'sort_order'];

    public function mosqueFacility()
    {
        return $this->belongsTo(MosqueFacility::class);
    }
}
