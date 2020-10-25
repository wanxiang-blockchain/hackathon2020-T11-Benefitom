<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-27
 * Time: 12:11
 */

namespace App\Utils;

use Illuminate\Support\Facades\Log;
use Qcloud\Sms\SmsSingleSender;

class QcloudSms
{

    const APPID = '1400181738';
    const APPKEY = '6ff994a450ceea0ada44878c142a9b40';

    public static function send($nationcode, $phone, $code){
        try {
            $ssender = new SmsSingleSender(self::APPID, self::APPKEY);
            $params = [$code];
            $templateId = $nationcode == "86" ? '273547' : '273530';
            $result = $ssender->sendWithParam($nationcode, $phone, $templateId,
                $params, "绍德堂", "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result, true);
            return isset($rsp['result']) && $rsp['result'] === 0;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 身份认证审核通过
     * @param $nationcode
     * @param $phone
     * @return bool
     */
    public static function profileVerifyNotice($nationcode, $phone){
        try {
            $ssender = new SmsSingleSender(self::APPID, self::APPKEY);
            $params = ['****' . substr($phone, -4)];
            $templateId = '279395';
            empty($nationcode) && $nationcode = '86';
            $result = $ssender->sendWithParam($nationcode, $phone, $templateId,
                $params, "绍德堂", "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result, true);
            return isset($rsp['result']) && $rsp['result'] === 0;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 身份认证未通过
     * @param $nationcode
     * @param $phone
     * @return bool
     */
    public static function profileRejectNotice($nationcode, $phone){
        try {
            $ssender = new SmsSingleSender(self::APPID, self::APPKEY);
            $params = ['****' . substr($phone, -4)];
            $templateId = '279396';
            empty($nationcode) && $nationcode = '86';
            $result = $ssender->sendWithParam($nationcode, $phone, $templateId,
                $params, "绍德堂", "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result, true);
            return isset($rsp['result']) && $rsp['result'] === 0;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

}