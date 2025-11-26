<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MosqueManager extends Model
{
    protected $table = 'mosque_managers';
    protected $guarded = [];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
