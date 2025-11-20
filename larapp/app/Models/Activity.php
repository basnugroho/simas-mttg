<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'activity_name',
        'created_by',
        'category',
    ];

    public function mosques()
    {
        return $this->belongsToMany(Mosque::class, 'activity_mosque')
            ->withPivot(['note'])
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
