<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRegionRole extends Model
{
    protected $table = 'user_region_roles';

    protected $fillable = [
        'user_id', 'role_key', 'region_id', 'created_by', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function region()
    {
        return $this->belongsTo(Regions::class, 'region_id');
    }
}
