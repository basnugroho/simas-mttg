<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeletion extends Model
{
    protected $table = 'user_deletions';

    protected $fillable = [
        'deleted_user_id',
        'deleted_by_user_id',
        'payload',
        'deleted_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'deleted_at' => 'datetime',
    ];
}
