<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/6
 * Time: 上午8:41
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Msg extends Model
{
	protected $fillable = [
		'msg', 'phone', 'tempcode'
	];

	public static function record($param, $phone, $temp)
	{
		return self::create([
			'msg' => json_encode($param),
			'phone' => $phone,
			'tempcode' => $temp
		]);
	}

}