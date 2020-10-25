<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/26
 * Time: 下午7:53
 */

namespace App\Http\Controllers\Rong;


use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Asset;
use App\Model\Finance;
use App\Model\Member;
use App\Model\Product;
use App\Model\UserProduct;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Support\Facades\DB;

class RongController extends Controller
{

	public function index()
	{
		return '建设中';
		$models = Product::where('enable', Product::ENABLE)
			->orderBy('created_at', 'desc')
			->get()->all();

		$member = Member::current();
		// 我购买的理财
		// TODO 如果给用户余额？
		// 1、定时任务！ 2、人工审核！3、查表变更

		return view('front.rong.index', compact('models', 'member'));
	}

	public function buy($id, AccountService $accountService)
	{
		return '建设中';
		// 我的可有余额
		$member = Member::current();
		$balance = $accountService->balance($member->id);

		// 可买数量
		$model = Product::where('id', $id)->first();
//		$canSaleAmount = $model->amount - $model->sold_amount;
		$canBuy =  intval($balance / $model->price);

		$key = trim(file_get_contents("../rsa_1024_pub.pem"));

		return view('front.rong.buy', compact('model', 'balance', 'canBuy', 'key'));
	}

	public function postBuy(Request $request, ValidatorService $validatorService, AccountService $accountService)
	{

		return '建设中';
		$data = $request->all();
		$validator = $validatorService->checkValidator([
			'id' => 'required',
			'amount' => 'required|numeric',
			'tradePassword' => 'required'
		], $data);

		if($validator['code'] !== 200){
			return $validator;
		}

		$product = Product::find($data['id']);

		if(empty($product)) {
			return ResUtil::error(201, '数据有误，请重试');
		}

		$member = Member::current();
		$account = $member->account;

		// 验证交易密码
		if(empty($member->account->trade_pwd)){
			return ['code'=>220,'data'=>'请先设置交易密码, <a target="_blank" href="/member/resetTradePassword">去设置</a>'];
		}

		$tradePassword = $data['tradePassword'];

		// 半小时内可使用交易remember_token
		$decrypted = "";
		if (openssl_private_decrypt(base64_decode($tradePassword),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
			$tradePassword = $decrypted;
		}

		if(!(\Hash::check($tradePassword, $member->account->trade_pwd))) {
			return ['code'=>202, 'data'=>'交易密码不正确'];
		}

		try{
			// 可买数量
			$account_id = $accountService->getAccountId($member->id);
			DB::beginTransaction();
			$balance = $accountService->balance($member->id);
//			$canSaleAmount = $product->amount - $product->sold_amount;

//			if($data['amount'] > $canSaleAmount) {
//				return ResUtil::error(202, "购买数量不可超过$canSaleAmount");
//			}

			if($data['amount'] > intval($balance / $product->price)) {
				return ResUtil::error(202, '余额不足，请前往充值, <a target="_blank" href="/member/recharge">去充值</a>');
			}

			// 购买产品
			$end_at = date('Y-m-d', strtotime("+{$product->duration} month"));
			if(!UserProduct::create([
				'member_id' => $member->id,
				'product_id' => $product->id,
				'stat' => Product::STAT_HOLD,
				'end_at' => $end_at,
				'amount' => $data['amount']
			])) {
				throw new \Exception('产品购买失败');
			}

			// 修改产品剩余量
			$product->sold_amount += $data['amount'];
			if(!$product->update()) {
				throw new \Exception('产品购买失败');
			}

			// 扣除余额
			$consumeAmount = ($product->price * $data['amount']);
			$accountService->addAsset($account_id, Account::BALANCE, -1 * $consumeAmount, '');

			// 记录finance
			$r = FinanceService::record($member->id, Account::BALANCE, Finance::RONG,-1 * $consumeAmount,
				0, "购买{$product->name}消费{$consumeAmount}元");
			if(!$r) {
				throw new \Exception('数据库写入失败');
			}

			DB::commit();
			return ResUtil::ok('购买成功');

		}catch (\Exception $e) {
			\Log::error($_REQUEST);
			\Log::error($e->getTraceAsString());
			DB::rollBack();
			return ['code'=>202, 'data'=>$e->getMessage()];
		}

	}

	public function detail($id)
	{
		return '建设中';
		$model = Product::where('id', $id)->first();
		return view('front.rong.detail', compact('model'));
	}

	public function protocol()
	{
		return '建设中';
		return view('front.rong.protocol');
	}

}