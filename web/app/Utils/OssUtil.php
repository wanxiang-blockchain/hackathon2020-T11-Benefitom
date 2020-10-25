<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-22
 * Time: 11:23
 */

namespace App\Utils;

use OSS\OssClient;

class OssUtil
{
    const OSS_ID = 'LTAIesdlWH4rorzG';
    const OSS_KEY = 'b7hhAm0ZlwKFPq1yarrsebM8SxeCtm';
    const ENDPOINT = 'oss-cn-beijing.aliyuncs.com';
    const BUCKET = 'dlyj-cms';

    /**
     * @var
     * @type OssClient
     */
    private static $oss;

    /**
     * @desc getOss
     * @return OssClient
     */
    public static function getOss()
    {
        if(!static::$oss instanceof OssClient)
            static::$oss = new OssClient(self::OSS_ID, self::OSS_KEY, self::ENDPOINT);
        return static::$oss;
    }

    public static function fetchGetSignUrl($object, $ssl=true)
    {
        if (empty($object)){
            return "";
        }
        try{
            if($object[0] == '/'){
                $object = substr($object, 1, strlen($object) -1);
            }
            if (strpos($object, 'http') === 0){
                return $object;
            }
            return $ssl ? 'https://dlyj-cms.oss-cn-beijing.aliyuncs.com/' . $object : 'http://dlyj-cms.oss-cn-beijing.aliyuncs.com/' . $object;
            $oss = static::getOss();
            if ($ssl)
                $oss->setUseSSL(true);
            $url = $oss->signUrl(self::BUCKET, $object, 1800);
//			$url = str_replace('saye.oss-cn-beijing.aliyuncs.com', 'static.saye-sports.com', $url);
            return $url;
        }catch (\Exception $e){
            return $object;
        }

    }

    public static function fetchPutSignUrl($object)
    {
        $oss = static::getOss();
        return $oss->signUrl(self::BUCKET, $object, 1800, OssClient::OSS_HTTP_PUT);
    }

    public static function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

    public static function fetchSts($type=2)
    {
        $type = $type == 1 ? 'videos' : 'images';

        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = static::gmt_iso8601($end);

        // 根据type 设置 video image
        $dir = $type . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . rand(10, 99) . '/';

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, self::OSS_KEY, true));
        $response = array();
        $response['accessid'] = self::OSS_ID;
        $response['host'] = 'https://' . self::BUCKET . '.' . self::ENDPOINT;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        return $response;
    }
    public static function imgPath()
    {
        return '/images/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
    }
}