<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/29
 * Time: 上午9:20
 */

namespace App\Http\Controllers\Admin;


trait ResponseTrait{

	public function success($data='成功', $code='200')
	{
		return ['code' => $code, 'data' => $data];
	}

	public function error($data='失败', $code='201')
	{
		return ['code' => $code, 'data' => $data];
	}

}