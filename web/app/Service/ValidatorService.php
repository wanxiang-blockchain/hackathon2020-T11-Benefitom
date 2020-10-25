<?php
namespace App\Service;
use Illuminate\Support\Facades\Validator;
class ValidatorService {

    public function checkValidator($rule, $data)
    {
        $validator = Validator::make($data, $rule);
        $validator->setAttributeNames([
            'duration' => '期限',
            'rate' => '利率',
            'amount' => '数量',
            'sold_amount' => '已售数量',
	        'info' => '说明',
	        'tradePassword' => '交易密码',
            'name' => '姓名',
            'province' => '省份',
            'city' => '市',
            'area' => '县/区',
            'addr' => '地址',
            'phone' => '手机号',
	        'idno' => '身份证号',
	        'verifycode' => '验证码',
            'invite_code' => '邀请码',
            'card' => '卡号',
            'bank' => '开户行',
            'headbank' => '所属银行',
            'sex' => '性别',
            'id_img' => '身份证正面照',
            'id_back_img' => '身份证背面照',
            'id_hold_img' => '手持身份证照',
        ]);
        $msg = '';
        if ($validator->fails()) {
            $message = $validator->errors();
            foreach ($message->all() as $message) {
                $msg .= $message."\n";
            }
        }
        if($msg) {
            return ['code'=>202, 'data'=>$msg];
        }
        return ['code'=>200, 'data'=>''];
    }

}