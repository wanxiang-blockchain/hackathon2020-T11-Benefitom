<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/4/25
 * Time: 上午11:12
 */

namespace App\Http\Controllers\Front;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Artbc;
use App\Model\ArtbcLog;
use App\Model\Member;
use App\Model\Score;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArtbcController extends Controller
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function flows()
	{
		$request = $this->request;
		$member = Member::current();
		$models = ArtbcLog::where('member_id', $member->id)->orderByDesc('id')->paginate(10);
		$artbc = Artbc::fetchByMemberId($member->id);

		return view('front.member.center.artbcflow', compact('models', 'artbc'));
	}

	public function ti()
	{
		$request = $this->request;
		$member = Member::current();
		if ($request->isMethod('GET')) {
			$key = trim(file_get_contents("../rsa_1024_pub.pem"));
			$data = ['balance' => Artbc::fetcyBalanceByMemberId($member->id), 'key' => $key];

			return view('front.member.artbc.ti', $data);
		} else {
			$amount = $request->get('amount');
			$eth_addr = $request->get('eth_addr');
			$tradePassword = $request->get('tradePassword');
			$account = $member->account;

			if (empty($amount) || empty($eth_addr)) {
				return ['code' => 201, 'data' => '请输入提取信息'];
			}

			if (empty($amount) || $amount < 100) {
				return ['code' => 201, 'data' => '单次提币不得少于100'];
			}

			if (strtolower(substr($eth_addr, 0, 2)) !== '0x' || strlen($eth_addr) !== 42) {
				return ResUtil::error(201, '钱包地址格式不正确');
			}

			if (empty($account->trade_pwd)) {
				return ['code' => 220, 'data' => '请先设置交易密码, <a target="_blank" href="/member/resetTradePassword">去设置</a>'];
			}

			// 半小时内可使用交易remember_token
			$decrypted = "";
			if (openssl_private_decrypt(base64_decode($tradePassword), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
				$tradePassword = $decrypted;
			}

			if (!(\Hash::check($tradePassword, $account->trade_pwd))) {
				return ['code' => 202, 'data' => '交易密码不正确'];
			}

			\DB::beginTransaction();
			try {
				// 添加artbc_log，养活artbc
				$artbc = Artbc::fetchByMemberId($member->id);
				if (!$artbc || $artbc->balance < $amount) {
					throw new TradeException('持币数量不够');
				}
				ArtbcLog::add($member->id, -1 * $amount, ArtbcLog::TYPE_TIBI, $eth_addr);
				\DB::commit();

				return ResUtil::ok();
			} catch (TradeException $e) {
				\DB::rollBack();

				return ResUtil::error(201, $e->getMessage());
			} catch (\Exception $e) {
				\Log::error($e->getTraceAsString());
				\DB::rollBack();

				return ResUtil::error(201, $e->getMessage());
			}

		}
	}


	// 获取某个用户的币信息
	public function balance(Request $request, ValidatorService $validatorService)
	{
		$uid = $request->input('uid', '');
		if (empty($uid)) {
			return ResUtil::error(203, 'invalid params');
		}

		$member = Member::where('uid', $uid)->select('id')->first();

		if (!$member) {
			return ResUtil::error(204, 'user is not exists');
		}

		$score = Artbc::fetchByMemberId($member->id);

		$score = isset($score['balance']) ? $score['balance'] : 0;

		return ResUtil::ok(compact('score'));

	}

	// 消费币
	public function consume(Request $request, ValidatorService $validatorService)
	{
		/**
		 * 1、取ArTBC，看够不够
		 * 2、减少ArTBC、
		 */
		$data = $request->all();
		$validator = $validatorService->checkValidator([
			'uid' => 'required',
//			'type' => 'required|numeric',
			'score' => 'required|numeric',
			'order_id' => 'required'],
			$data);
		if ($validator['code'] != 200) {
			return $validator;
		}

		$member = Member::where('uid', $data['uid'])->first();

		if (!$member) {
			return ResUtil::error(204, 'user is not exists');
		}
		DB::beginTransaction();
		try{
			$score = Artbc::fetchByMemberId($member->id);

			if (empty($score['balance']) || $score['balance'] < $data['score']) {
				DB::rollBack();

				return ResUtil::error(201, 'ArTBC不足');
			}

			ArtbcLog::add($member->id, -1 * $data['score'], ArtbcLog::TYPE_CONSUME, '', $data['order_id']);
			DB::commit();
			$score = Artbc::fetchByMemberId($member->id);

			return ResUtil::ok(['score' => $score->balance]);
		} catch (\Exception $e){
			DB::rollBack();
			return ResUtil::error(205, $e->getMessage());
		}

	}
}