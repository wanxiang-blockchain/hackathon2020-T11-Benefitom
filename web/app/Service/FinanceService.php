<?php
/**
 * Created by PhpStorm.
 * User: Yolanda
 * Date: 2017/3/18
 * Time: 15:45
 */

namespace App\Service;


use App\Model\Asset;
use App\Model\Finance;

class FinanceService
{
    function subscription($member_id, $asset_type, $balance, $amount,$content=''){
	    FinanceService::record($member_id, $asset_type, 3, $balance, $amount, $content);
    }
    function  adminRecharge($member_id,$asset_type,$type,$balance,$amount,$content){
        FinanceService::record($member_id, $asset_type, $type, $balance, $amount, $content);
    }

	/**
	 * 记录交易明细
	 * @desc record
	 * @param $member_id
	 * @param $asset_type
	 * @param $type
	 * @param $balance
	 * @param $amount
	 * @param $content
	 * @return mixed
	 */
	public static function record($member_id,$asset_type,$type,$balance,$amount,$content, $order_no='')
	{
		return Finance::create([
			'member_id'=>$member_id,
			'type'=>$type,
			'asset_type'=>$asset_type,
			'content'=>$content,
			'balance'=>$balance,
			'amount'=>$amount,
			'order_no' => $order_no,
			'after_amount' => Asset::fetchBalanceAmount(AccountService::fetchAccountId($member_id))
		]);
	}

}