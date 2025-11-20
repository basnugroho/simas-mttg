<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityUnit extends Model
{
    protected $fillable = ['name', 'slug'];

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'unit_id');
    }

    public function mosqueFacilityAssignments()
    {
        return $this->hasMany(MosqueFacility::class, 'unit_id');
    }
}
