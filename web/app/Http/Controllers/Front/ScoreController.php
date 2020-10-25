<?php

namespace App\Http\Controllers\Front;

use App\Model\Member;
use App\Model\Score;
use App\Model\ScoreLog;
use App\Service\AccountService;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
	public $accountService;

	public function __construct(AccountService $accountService)
	{
		$this->accountService = $accountService;
	}

	// 获取某个用户的积分信息
	public function get(Request $request, ValidatorService $validatorService)
	{
		$uid = $request->input('uid', '');
		if(empty($uid)) {
			return ResUtil::error(203, 'invalid params');
		}

		$member = Member::where('uid', $uid)->select('id')->first();

		if (!$member) {
			return ResUtil::error(204, 'user is not exists');
		}

		$score = Score::where('account_id', $this->accountService->getAccountId($member->id))
			->select('score')->first();

		$score = isset($score['score']) ? $score['score'] : 0;

		return ResUtil::ok(compact('score'));

	}

	// 消费积分
	public function consume(Request $request, ValidatorService $validatorService)
	{
		/**
		 * 1、取积分，看够不够
		 * 2、减少积分、
		 */
		$data = $request->all();
		$validator = $validatorService->checkValidator([
			'uid' => 'required',
			'type'  => 'required|numeric',
			'score' => 'required|numeric',
			'order_id' => 'required'
		], $data);
		if($validator['code'] != 200) {
			return $validator;
		}

		$member = Member::where('uid', $data['uid'])->first();

		if (!$member) {
			return ResUtil::error(204, 'user is not exists');
		}
		DB::beginTransaction();
		$account_id = $this->accountService->getAccountId($member->id);
		$score = Score::where('account_id', $account_id)
			->first();
		if (empty($score['score']) || $score['score'] < $data['score']) {
			DB::rollBack();
			return ResUtil::error(201, '积分不足');
		}
		$score->score -= abs($data['score']);
		if(!$score->save()) {
			return ResUtil::error(201, '积分扣除失败');
			DB::rollBack();
		}
		ScoreLog::create([
			'score' => $data['score'],
			'account_id' => $account_id,
			'order_id' => $data['order_id'],
			'type' => $data['type']
		]);
		DB::commit();
		return ResUtil::ok(['score' => $score->score]);
	}
}
