<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/8/19
 * Time: 下午8:44
 */

namespace App\Http\Controllers\Api;


use App\Model\Artbc;
use App\Model\Artbc\ArtbcUnlock;
use App\Model\Member;
use App\Utils\ResUtil;

class ArtbcUnlockController
{
	public function info()
	{
		/**
		 * {
		"code":200,  // 成功为 200，失败为 其他
		"data":{
		"amount": 10000, // 锁仓量
		"balance": 2500,  // 可使用余额
		"last_unlock_day": "2018-08-15"   // 上一次 解锁日期
		},
		"userMsg":"成功"
		}

		 */
		$member = Member::apiCurrent();
		$artbcUnlocks = ArtbcUnlock::where('member_id', $member->id)
			->where('stat', ArtbcUnlock::STAT_UNLOCKING)
			->get();
		$artbc = Artbc::fetchByMemberId($member->id);
		// 锁仓总量
		$amount = 0;
		$last_unlock_day = '0000-00-00';
		// 可提取量
		$balance = $artbc ? $artbc->balance : 0;
		// 开始释放日期
        $start_unlock_day = '0000-00-00';
		foreach ($artbcUnlocks as $artbcUnlock){
			$amount += $artbcUnlock->amount - $artbcUnlock->unlocked_amount;
			if ($artbcUnlock->last_unlock_day > $last_unlock_day) {
				$last_unlock_day = $artbcUnlock->last_unlock_day->toDateTimeString();
			}
			$start_unlock_day = $artbcUnlock->start_unlock_day;
		}
		return ResUtil::ok(compact('amount', 'balance', 'last_unlock_day', 'start_unlock_day'));
	}
}