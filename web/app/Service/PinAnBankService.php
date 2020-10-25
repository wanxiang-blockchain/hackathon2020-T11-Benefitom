<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
class PinAnBankService
{

    public $return_url = 'http://www.transfer.com/back';
    public $notify_url = 'http://www.transfer.com/back_notify';

    const MASTER_ID = '2000311146';
    const DOMAIN = 'https://my-uat1.orangebank.com.cn/';
    //开卡服务
    const OPEN_CARD = self::DOMAIN.'corporbank/UnionAPI_Open.do';
    const CARD_LIST = self::DOMAIN.'khpayment/UnionAPI_Opened.do';

    //开卡
    public function openCard()
    {
        $data = [
            'masterId'=>self::MASTER_ID,
            'customerId'=>\Auth::guard('front')->id(),
            'orderId'=>self::MASTER_ID.date('Ymd').randStr(8, 'NUMBER'),
            'dateTime'=>date('YmdHis'),
        ];
        $og_data = XMLService::createArray($data);
        $si_data = $this->sign($og_data);
        $orig = urlencode(base64_encode($og_data));
        $sign = urlencode(base64_encode($si_data));
        $returnurl = $this->return_url;
        $notice = $this->notify_url;
        return view('test', compact('sign', 'orig', 'returnurl', 'notice', 'og_data', 'si_data'));
    }

    //获取卡列表
    public function cardList()
    {
        $data = [
            'masterId'=>self::MASTER_ID,
            'customerId'=>\Auth::guard('front')->id(),
        ];
        $result = $this->initData($data, self::CARD_LIST, []);
        if($result['code'] =! 200) {
            return $result;
        }
        $source_data = $result['data']['orig']['kColl']['iColl']['kColl'];
        $return_data = [];
        foreach ($source_data as $key=>$source_datum) {
            $call =  call_user_func_array([$this, 'eachXmlData'], $source_datum);
            if ($call) {
                $return_data[$key] = $call;
            }
        }
        return ['code'=>200, 'data'=>$return_data];
    }

    //发送支付验证码
    public function sendBankSms()
    {
        $data = [

        ];

    }

    //支付交易







    //组合数据
    public function initData($data, $url, $ext = [])
    {
        $og_data = XMLService::createXML($data);
        $si_data = $this->sign($og_data);
        $orig = base64_encode($og_data);
        $sign = base64_encode($si_data);
        $client = new Client();
        try {
            $request_data = [
                'orig' => $orig,
                'sign' => $sign,
            ];
            $response = $client->post(
                $url,
                [
                    'form_params' => array_merge($request_data, $ext)
                ]
            );
            $response_data = trim($response->getBody()->getContents());
            $t = explode("\r\n", $response_data);
            $key_values = [];
            foreach ($t as $line) {
                list($k, $v) = explode("=", $line);
                if (in_array($k, ['sign', 'orig'])) {
                    $key_values[$k] = iconv('gbk', 'utf-8', base64_decode(urldecode($v)));
                } else {
                    $key_values[$k] = $v;
                }
            }
            $key_values['orig'] = XMLService::createArray($key_values['orig']);
            return ['code' => 200, 'data' => $key_values];
        } catch (ServerException $e) {
            return ['code' => 202, 'massge' => $e->getMessage()];
        }
    }


    //签名验证
    public function sign($strData)
    {
        $signedMsg = "";
        $privateKey = file_get_contents(storage_path('cert/private_key.pem'));
        $key = openssl_pkey_get_private($privateKey);
        $strData = iconv("utf-8", "gbk", $strData);
        if (openssl_sign($strData, $signedMsg, $key, OPENSSL_ALGO_MD5)) {
            $signedMsg = bin2hex($signedMsg);
        }

        return $signedMsg;
    }

    protected function eachXmlData($source_datum)
    {
        $arr = [];
        if (is_array($source_datum)) {
            sort($source_datum);
            $data = array_filter($source_datum);
            if ($data) {
                foreach ($source_datum as $key => $item) {
                    if ($item) {
                        $arr[$item['id']] = $item['value'];
                    }

                }
            }
        }
        return $arr;
    }
}
