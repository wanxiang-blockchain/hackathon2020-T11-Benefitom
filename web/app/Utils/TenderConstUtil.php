<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/11/10
 * Time: 下午12:36
 */

namespace App\Utils;


class TenderConstUtil
{
	const PARITY = 10;  // 小红花与人民币汇率

	public static function guessPrice()
	{
		return 1 * self::PARITY;
	}

}