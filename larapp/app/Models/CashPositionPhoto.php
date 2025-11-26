<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashPositionPhoto extends Model
{
    protected $table = 'cash_position_photos';
    protected $guarded = [];

    public function cashPosition()
    {
        return $this->belongsTo(CashPosition::class);
    }
}
