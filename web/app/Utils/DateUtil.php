<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/18
 * Time: 下午12:02
 */

namespace App\Utils;


class DateUtil
{
	public static function now()
	{
		return date('Y-m-d H:i:s');
	}

	public static function today()
    {
        return date('Y-m-d 00:00:00');
    }

    public static function todayDate()
    {
        return date('Ymd');
    }

}