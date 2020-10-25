<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/6/11
 * Time: 上午11:03
 */

namespace App\Utils;


use EasyWeChat\Core\Exceptions\HttpException;

class HttpUtil
{
	public static function post($url, $content, $needLog=true)
	{
		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER  => false,
			CURLOPT_CONNECTTIMEOUT => 50, // timeout on connect
			CURLOPT_TIMEOUT => 100, // timeout on response
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($content),
			CURLOPT_CUSTOMREQUEST => 'POST',
		];

		$ch = curl_init($url);
		if ($ch) {
			curl_setopt_array($ch, $options);
			$data = curl_exec($ch);
			curl_close($ch);
			if($needLog){
				$logInfo = [
					'url' => $url,
					'params' => $content,
					'result' => $data
				];
				\Log::debug($logInfo);
			}
			return $data;
		} else {
			throw new HttpException("curl $url failed");
		}
	}

    public static function json($url, $content, $needLog=true)
    {
        $ch = curl_init($url);
        if ($ch) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($content))
            );
            $data = curl_exec($ch);
            curl_close($ch);
            if($needLog){
                $logInfo = [
                    'url' => $url,
                    'params' => $content,
                    'result' => $data
                ];
                \Log::debug($logInfo);
            }
            return $data;
        } else {
            throw new HttpException("curl $url failed");
        }
    }

	public static function get($url, $content, $needLog=true)
	{
		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER  => false,
			CURLOPT_CONNECTTIMEOUT => 50, // timeout on connect
			CURLOPT_TIMEOUT => 100, // timeout on response
			CURLOPT_POST => false,
			CURLOPT_CUSTOMREQUEST => 'GET',
		];

		$url .= '?' . http_build_query($content);

		$ch = curl_init($url);
		if ($ch) {
			curl_setopt_array($ch, $options);
			$data = curl_exec($ch);
			curl_close($ch);
			if($needLog){
				$logInfo = [
					'url' => $url,
					'params' => $content,
					'result' => $data
				];
				\Log::debug(json_encode($logInfo));
			}
			return $data;
		} else {
			throw new HttpException("curl $url failed");
		}
	}

}