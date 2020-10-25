<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-07
 * Time: 16:13
 */

namespace App\Utils;


use App\Exceptions\TradeException;
use App\Model\Member;
use Illuminate\Support\Facades\Log;

class DissysPush
{

    const HOST = 'https://disfront.tangartbank.com';

    public static function appkey()
    {
        $appkey = env('DIS_FRONT_APPKEY', '');
        if (empty($appkey)){
            throw new TradeException('empty appkey');
        }
        return $appkey;
    }

    public static function sign($data)
    {
        ksort($data);
        $timestamp = time();
        $signStr = implode('', $data) . static::appkey() . $timestamp;
        $data['timestamp'] = $timestamp;
        $data['sign'] = sha1($signStr);
        Log::debug('sign: ', [
            'data' => $data,
            'signStr' => $signStr
        ]);
        return $data;
    }

    /**
     * 推送注册数据
     */
    public static function reg($member_id, $up_phone)
    {
//        return true;
        if (env('APP_ENV', 'prod') !== 'prod'){
            return true;
        }
        $member = Member::find($member_id);
        if (empty($member)){
            throw new TradeException('unknow member_id');
        }
        $data = [
            'phone' => $member->phone,
            'up_phone' => $up_phone
        ];
        $url = self::HOST . '/bt_link/bt_get_member';
        $data = static::sign($data);
        $ret = HttpUtil::json($url, json_encode($data));
        $ret = json_decode($ret, true);
        if (isset($ret[0]['code']) && $ret[0]['code']  == 200){
            return true;
        }
        if (isset($ret['code']) && $ret['code']   == 200){
            return true;
        }
        return false;
    }

    /**
     * order_code:"sta12345678", //唯一,--避免重复提交
    phone:"13800138000", //获得积分的会员手机号码
    score:1000 //整数,必须是1000的倍数
     * @param $member_id
     * @param $score
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public static function score($order_code, $phone, $score)
    {
        return true;
        if (env('APP_ENV', 'prod') !== 'prod'){
            return true;
        }
        $data = [
            'order_code' => $order_code,
            'phone' => $phone,
            'score' => $score
        ];
        $url = self::HOST . '/bt_link/bt_get_stage';
        $data = static::sign($data);
        $ret = HttpUtil::json($url, json_encode($data));
        $ret = json_decode($ret, true);
        if (isset($ret[0]['code']) && $ret[0]['code']  == 200){
            return true;
        }
        if (isset($ret['code']) && $ret['code']   == 200){
            return true;
        }
        return false;
    }

    public static function appendParent($phone, $up_phone)
    {
//        return true;
        if (env('APP_ENV', 'prod') !== 'prod'){
            return true;
        }
        if (empty($phone) || empty($up_phone)){
            throw new TradeException('empty phone in appendParent');
        }
        $data = [
            'phone' => $phone,
            'up_phone' => $up_phone
        ];
        $url = self::HOST . '/bt_link/bt_get_up_member';
        $data = static::sign($data);
        $ret = HttpUtil::json($url, json_encode($data));
        $ret = json_decode($ret, true);
        if (isset($ret[0]['code']) && $ret[0]['code']  == 200){
            return true;
        }
        if (isset($ret['code']) && $ret['code']   == 200){
            return true;
        }
        return false;
    }
}