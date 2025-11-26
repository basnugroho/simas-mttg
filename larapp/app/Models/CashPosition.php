<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashPosition extends Model
{
    protected $table = 'cash_positions';
    protected $guarded = [];
    
    protected $dates = ['period_start','period_end'];
    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'nominal' => 'decimal:2',
    ];

    protected $attributes = [
        'is_deleted' => false,
    ];

    protected $fillable = ['mosque_id','period_start','period_end','nominal','note','is_deleted','edited_by','edited_at','edit_note'];

    public function photos()
    {
        return $this->hasMany(CashPositionPhoto::class);
    }

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
