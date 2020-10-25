<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/8/19
 * Time: 下午8:44
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Artbc\BtScore;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\ListModel;
use App\Model\Member;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Twilio\Rest\Api;

class BtScoreUnlockController extends Controller
{

    public function index()
    {
        $member = Member::apiCurrent();
        $query = BtScoreUnlock::where('member_id', $member->id)
            ->where('stat', BtScoreUnlock::STAT_UNLOCKING);
        $listModel = new ListModel($query);
        $models = $listModel->fetchModels(['id', 'amount', 'unlocked_amount', 'created_at', 'last_unlock_time']);
        $count = count($models);
        return ApiResUtil::ok([
            'hasMore' => $count == ApiResUtil::PAGENATION,
            'list' => $models->toArray()
        ]);
    }
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
		$artbcUnlocks = BtScoreUnlock::where('member_id', $member->id)
			->where('stat', BtScoreUnlock::STAT_UNLOCKING)
			->get();
		$btscore = BtScore::fetchByMemberId($member->id);
		$amount = 0;
		$last_unlock_time = strtotime('1970-01-01');
		$balance = $btscore ? $btscore->score: 0;
		foreach ($artbcUnlocks as $artbcUnlock){
			$amount += $artbcUnlock->amount - $artbcUnlock->unlocked_amount;
			if ($artbcUnlock->last_unlock_time > $last_unlock_time) {
				$last_unlock_time = $artbcUnlock->last_unlock_time;
			}
		}
		$last_unlock_day = date('Y-m-d') ;
		$btscore = BtScore::fetchByMemberId($member->id);
		$shopping_score = $btscore ? $btscore->shopping_score : 0;
//        $rec_score = $btscore ? $btscore->rec_score : 0;
		return ResUtil::ok(compact('amount', 'balance', 'last_unlock_day', 'shopping_score'));
	}
}