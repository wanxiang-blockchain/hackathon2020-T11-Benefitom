<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/27
 * Time: 上午11:44
 */

namespace App\Http\Controllers\Tender;


use App\Http\Controllers\Controller;
use App\Model\Member;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderFlow;
use App\Model\Tender\TenderMsg;
use App\Model\Tender\TenderSignup;
use App\Utils\ResUtil;
use App\Utils\TenderConstUtil;
use Illuminate\Support\Facades\DB;

class SignupController extends Controller
{
	/**
	 * @desc hasSigned
	 * 判断今天是否否签到以及上一个签到日期
	 */
	public function hasSigned()
	{
		$ret = ['code' => 200, 'data' => ['hasSigned' => 1, 'addup' => 0]]	;
		$member = Member::current();
		$model = TenderSignup::where('member_id', $member->id)
			->where('day', date('Y-m-d'))
			->first();
		if ($model) {
			// 今天已经签到
			return $ret;
		}

		// 取上一个签到
		$model = TenderSignup::where('member_id', $member->id)
			->orderByDesc('created_at')->first();
		if (empty($model)){
			// 如果从来没有签到过
			return ['code' => 200, 'data' => ['hasSigned' => 0, 'addup' => 1]]	;
		}

		// 看是否连续 且 是否满7天
		if ($model->day == date('Y-m-d', time() - 86400) && $model->addup < 7){
			// 连续
			return ResUtil::ok(['hasSigned' => 0, 'addup' => $model->addup+1]);
		}

		return ResUtil::ok(['hasSigned' => 0, 'addup' => 1]);
	}

	// 签到
	public function signup()
	{
		$member = Member::current();
		\Log::info('signup: ' . $member->id);
		DB::beginTransaction();
		try{
			$model = TenderSignup::where('member_id', $member->id)
				->where('day', date('Y-m-d'))
				->first();
			if ($model) {
				// 今天已经签到
				return ResUtil::ok();
			}

			// 取上一个签到
			$model = TenderSignup::where('member_id', $member->id)
				->orderByDesc('created_at')->first();

			// 看是否连续 且 是否满7天
			if ($model && $model->day == date('Y-m-d', time() - 86400) && $model->addup < 7){
				// 连续
				TenderSignup::create([
					'member_id' => $member->id,
					'day' => date('Y-m-d'),
					'addup' => ++$model->addup
				]);
				if ($model->addup >= 3) {
					// 如果是3天以上，赠送小红花
					TenderAsset::add($member->id, 1 * TenderConstUtil::PARITY, TenderFlow::TYPE_SIGNUP_GIFT);
					TenderMsg::setTempSignup($member->id, $model->addup, 1 * TenderConstUtil::PARITY);
				}
			}

			// 如果首次签到
			if(empty($model)){
				// 首次签到，赠送小红花
				TenderAsset::add($member->id, 1 * TenderConstUtil::PARITY, TenderFlow::TYPE_SIGNUP_GIFT);
				TenderMsg::setTempFirstSign($member->id, 1 * TenderConstUtil::PARITY);
				TenderSignup::create([
					'member_id' => $member->id,
					'day' => date('Y-m-d'),
					'addup' => 1
				]);
			}

			DB::commit();
			return ResUtil::ok();
		}catch (\Exception $e){
			DB::rollBack();
			return ResUtil::error(201, $e->getMessage());
		}
	}

}