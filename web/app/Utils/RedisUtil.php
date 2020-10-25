<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-27
 * Time: 18:16
 */

namespace App\Utils;


use Illuminate\Support\Facades\Redis;

class RedisUtil
{

    public static function set($key, $value, $expire=null)
    {
        Redis::set($key, $value);
        if ($expire){
            Redis::expire($key, $expire);
        }
    }

    public static function get($key)
    {
        return Redis::get($key);
    }

    public static function lpush($key, $value)
    {
        return Redis::lpush($key, $value);
    }

    public static function rpop($key)
    {
        return Redis::rpop($key);
    }

    public static function incrby($key, $value, $expire)
    {
        Redis::INCRBY($key, $value);
        if ($expire){
            Redis::expire($key, $expire);
        }

    }

}