<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TradeSet extends Model
{
    protected $table = 'trade_set';
    protected $fillable = [
        'asset_type', 'start', 'end', 'limit', 'rate','trade_start', 't_plus', 'start2', 'end2'
    ];

    public static function fetchOne($code)
    {
    	return static::where('asset_type', $code)->first();
    }

    public static function left($code)
    {

    }
}
