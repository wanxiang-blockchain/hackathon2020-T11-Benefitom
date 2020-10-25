<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/8/15
 * Time: 下午2:52
 */

namespace App\Utils;


class ApiResUtil
{

	const PAGENATION = 10;
	const LOGIN_FAIL = '账号不存在请前往注册';
	const NO_DATA = '数据不存在';
	const SAVE_FAIL = '保存失败，请稍候再试';

	const FUCKED_MAN = '您已被封号，如有疑问，请联系客服！';
	const SHUTUP_MAN = '您已被禁言，如有疑问，请联系客服！';

	const WRONG_CODE = '验证码错误或已过期';
	const WRONG_PARAMS = '参数有误';

	public static function error($msg="", $code=201, $data=[]){
		return [
			'code' => $code,
			'data' => $data,
			'userMsg' => $msg
		];
	}

	public static function ok($data=[], $msg='成功', $code=200)
	{
		return [
			'code' => $code,
			'data' => $data,
			'userMsg' => $msg
		];
	}

	public static function dropColumns(&$arr)
	{
		unset($arr['deleted_at']);
		foreach ($arr as $key => $item){
			if(is_array($item)){
				static::dropColumns($item);
			}
		}
	}

}