<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/19
 * Time: 下午2:20
 */

namespace App\Utils;


class ResUtil
{
	public static function error($code=201, $msg=""){
		return [
			'code' => $code,
			'data' => $msg
		];
	}

	public static function ok($data='成功', $code=200)
	{
		return [
			'code' => $code,
			'data' => $data
		];
	}

}