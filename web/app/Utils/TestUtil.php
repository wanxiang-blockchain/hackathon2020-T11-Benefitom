<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/7/6
 * Time: 上午8:09
 */

namespace App\Utils;


class TestUtil
{
	public static function memberWhiteList()
	{
		return [
			'15001204748',
			'18611010126',
			'13901184287',
			'13303512722'
		];
	}

	public static function isWhite($phone)
	{
		return in_array($phone, static::memberWhiteList());
	}

}