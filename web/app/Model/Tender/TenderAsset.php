<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/16
 * Time: 上午11:35
 */

namespace App\Model\Tender;


use App\Model\Member;
use App\Utils\TenderConstUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TenderAsset extends Model
{
	protected $fillable = [
		'member_id', 'amount'
	];

	public static function add($member_id, $amount, $type)
	{
		$model = static::where('member_id', $member_id)->first();
		if(!$model){
			$model = new static();
			$model->member_id = $member_id;
			$model->amount = $amount;
		}else{
			$model->amount += $amount;
		}
		if (!$model->save()) {
			throw new \Exception('数据保存失败 class:' . __CLASS__ . ' line:' . __LINE__);
		}

		TenderFlow::create([
			'member_id' => $member_id,
			'amount' => $amount,
			'type' => $type,
			'after_amount' => $model->amount
		]);
		return true;
	}

	/**
	 * 取某用户可提现数目
	 * @desc canWithdraw
	 * @param $member_id
	 * @return int
	 */
	public static function canWithdraw($member)
	{
		if($member instanceof Member) {
			$tenderAsset = $member->tender_asset;
		}elseif (is_numeric($member)) {
			$tenderAsset = TenderAsset::where('member_id', $member)->first();
		}else {
			return 0;
		}
		$canWithdraw = 0;
		if($tenderAsset) {
			$canWithdraw = $tenderAsset->amount - TenderFlow::giftCount($member->id);
		}

		return $canWithdraw / TenderConstUtil::PARITY;
	}

}