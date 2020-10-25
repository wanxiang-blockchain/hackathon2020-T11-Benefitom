<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-06
 * Time: 20:54
 */

namespace App\Utils;


use App\Model\Member;
use Illuminate\Support\Facades\Redis;

class DisVerify
{

    public static function makeTk($id)
    {
        $tk = randStr(60);
        Redis::set(RedisKeys::DIS_VERIFY_TK_PRE . $tk, $id);
        Redis::expire(RedisKeys::DIS_VERIFY_TK_PRE . $tk, 7200);
        return $tk;
    }

    public static function verifyTk($tk)
    {
        $id = Redis::get(RedisKeys::DIS_VERIFY_TK_PRE . $tk);
        if ($id){
            return Member::find(intval($id));
        }
        return null;
    }

}