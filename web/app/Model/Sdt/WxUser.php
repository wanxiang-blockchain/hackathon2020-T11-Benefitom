<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/19
 * Time: 下午1:54
 */

namespace App\Model\Sdt;


use Illuminate\Database\Eloquent\Model;

class WxUser extends Model
{

	protected $connection = 'sdt';
	protected $table = 'sdt_wxuser';

	public static function fetchBySsouid($uid)
	{
		$wx = static::where('uid', $uid)->first();
		if(empty($wx)) {
			$wx = [
				'nick' => '暂无昵称',
				'headimg' => '/tender/images/tx.png'
			];
		}
		return $wx;
	}

	public static function fetchByOpenid($openid)
	{
		$wx = static::where('openid', $openid)->first();
		if(empty($wx)) {
			$wx = [
				'nick' => '暂无昵称',
				'headimg' => '/tender/images/tx.png'
			];
		}
		return $wx;
	}

}