<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMosque extends Model
{
    protected $table = 'user_mosques';

    protected $fillable = ['user_id', 'mosque_id', 'created_by'];

    public function mosque()
    {
        return $this->belongsTo(Mosque::class, 'mosque_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
