<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-13
 * Time: 20:24
 */

namespace App\Utils;

Use Twilio\Rest\Client;


class SmsUtil
{
    const SID = 'AC2f1392f7337c7a7c5ed93a4da3e6930e';
    const TOKEN = 'eb7f1d73c04c5c31462ee74d7aaa4b91';

    public static function send($to, $body)
    {
        $client = new Client(static::SID, static::TOKEN);
        // Use the client to do fun stuff like send text messages!
        return $client->messages->create(
            $to,
            [
                'from' => '+19282183327',
                'body' => $body
            ]
        );
    }

    public static function verifyCode($to, $code, $nationcode='+86', $sign='艺行派')
    {
        if ($nationcode[0] != '+') {
            $nationcode = '+'.$nationcode;
        }
        $res = static::send( $nationcode . $to, "【{$sign}】您的验证码： {$code}" );
        if (empty($res->errorMessage)) {
            return ['code' => 0];
        }
        return ['code' => 201, 'data' => $res->errorMessage];
    }

}