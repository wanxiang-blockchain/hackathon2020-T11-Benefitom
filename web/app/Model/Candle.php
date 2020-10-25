<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Candle extends Model
{

	// 日k
	const TYPE_DAY = 0;
	// 分时
	const TYPE_MIN = 1;

	//
	protected $fillable = [
		'asset_code', 'type', 'name', 'value'
	];

	public static function rightType($type) {
		return in_array($type,  [0, 1, 5, 10, 15, 30, 60]);
	}
}
