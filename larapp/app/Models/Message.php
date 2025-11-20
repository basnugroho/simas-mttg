<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'subject', 'message', 'mosque_id'
    ];

    public function mosque()
    {
        return $this->belongsTo(\App\Models\Mosque::class);
    }
}
