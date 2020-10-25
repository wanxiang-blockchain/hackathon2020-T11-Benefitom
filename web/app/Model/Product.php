<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

	const ENABLE = 1;
	const DISABLE = 0;

	const STAT_HOLD = 1; // 持有
	const STAT_END  = 2; // 结束

	protected $fillable = [
		'name', 'price', 'duration', 'rate', 'amount', 'sold_amount', 'enable', 'info', 'picture', 'banner'
	];

	public function enable(){
		return $this->enable == self::ENABLE;
	}

	public function disabled()
	{
		return !$this->enable();
	}

}
