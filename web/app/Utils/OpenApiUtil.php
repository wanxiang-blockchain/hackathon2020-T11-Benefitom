<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-02-28
 * Time: 14:01
 */

namespace App\Utils;


use App\Exceptions\TradeException;

class OpenApiUtil
{
    public static function appkey()
    {
        $appkey = env('OPEN_API_APPKEY', 'LLdopoIInvcvsadf99323dK');
        if (empty($appkey)){
            throw new TradeException('empty appkey');
        }
        return $appkey;
    }
}