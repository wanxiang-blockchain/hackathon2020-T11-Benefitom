<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/1/17
 * Time: 下午1:37
 */

namespace App\Http\Controllers\Front;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\AccountFlow;
use App\Model\AlipayLogs;
use App\Model\Finance;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Utils\WxPayUtil;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Overtrue\Socialite\User;

class WxController extends Controller
{
	private $app;

	/**
	 * @var User
	 */
	private $user;

	function __construct()
	{
		$this->app=new Application(config('wechat'));
	}

	public function recharge(Request $request)
	{
		$amount = $request->get('amount');
		$data = WxPayUtil::recharge($amount);
		return $data;
	}

	public function callback(Request $request)
	{
		$param = $request->all();
		Log::info("进入微信支付回调,参数，" . json_encode($param));
		$app = new Application(config('wechat'));

		$response = $app->payment->handleNotify(function ($notify, $successful) {
			DB::beginTransaction();
			try{

				$order_no = $notify['out_trade_no'];
				Log::info('notify: ' . json_encode($notify));

				$pay_log = AlipayLogs::where('order_id', $order_no)->first();

				if (count($pay_log) == 0) {
					Log::error('orderid=' . $order_no. '没找到');
					return true;//没有这个订单就直接回复不需要再次接收了
				}
				if ($pay_log->status == 1){
					Log::error("order[$order_no] has been paied");
					return true;
				}

				$pay_log->paid_at = date('Y-m-d H:i:s');
				$pay_log->content = json_encode($notify);
				$pay_log->status = 1;
				if (!$pay_log->save()){
					throw new TradeException('服务器异常');
				}

				$financeService = new FinanceService();
				$accountService = new AccountService();
				$account_id = $accountService->getAccountId($pay_log->member_id);
				$accountService->addAsset($account_id, Account::BALANCE, $pay_log->money, '');
				$financeService->adminRecharge(
					$pay_log->member_id,
					Account::BALANCE,
					Finance::RE_CHANGE,
					$pay_log->money,
					'',  //  资产数量
					'通过微信充值'. $pay_log->money .'元'
				);
				$accountFlow = new AccountFlow();
				$accountFlow->create_log($pay_log->member_id, $pay_log->money, $pay_log->real_money, '微信充值');

				\DB::commit();

				return true;
			} catch (\Exception $e){
				Log::error($e->getTraceAsString());
				return false;
			}
		});

		$resContent = $response->getContent();
		Log::info('response to wx: ' . $resContent);
		$response->send();

		return $response;

	}
}