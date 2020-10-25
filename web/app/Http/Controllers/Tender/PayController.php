<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/14
 * Time: 上午10:09
 */

namespace App\Http\Controllers\Tender;


use App\Http\Controllers\Controller;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderFlow;
use App\Model\Tender\TenderOrder;
use App\Utils\TenderConstUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PayController extends Controller
{

	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 生成 32 位order_id 需要标识上前缀区分艺奖堂，于server.yigongpan.com/callback 接口中进行数据确认
	 * @desc rechart
	 * @return array
	 */
	public function callback(){
		$innerSign = 'LdaljlklkjIjnkasdjfn1sdf';
		$notify = $this->request->all();
		\Log::info('request : ' . json_encode($notify));
		\Log::info('POST: ' . json_encode($_POST));
		\Log::info('GET: ' . json_encode($_GET));
		\Log::info('phpinput: ' . file_get_contents("php://input"));

		if(empty($notify['out_trade_no'])) {
			return ['code' => 201, 'data' => 'error params'];
		}

		$orderid = $notify['out_trade_no'];

		if (empty($notify['innerSign']) || $notify['innerSign'] !== $innerSign) {
			return ['code' => 201, '签名失败'];
		}

		try{
			DB::beginTransaction();
			$order = TenderOrder::where('order_id', $orderid)->first();
			if (count($order) == 0) {
				\Log::error('order_id=' .$orderid . '没找到');
				throw new \Exception('订单不存在');
			}

			if($order->stat > 0){
				\Log::info('orderid=' .$orderid . ' 已处理过');
				throw new \Exception('订单已处理过');
			}

			if($notify['return_code'] !== 'SUCCESS') {
				throw new \Exception('支付宝通知失败');
			}

			if($notify['result_code'] !== 'SUCCESS') {
				$order->stat = 2;
				$order->save();
				DB::commit();
				return ['code' => 200, 'data' => 'done'];
			}

			// 更新订单状态
			$order->stat = 1;
			if(!$order->save()) {
				throw new \Exception('数据库写入失败');
			}

			// 添加小红花
			TenderAsset::add($order->member_id, $order->amount * TenderConstUtil::PARITY, TenderFlow::TYPE_RECHARGE);

			DB::commit();
			return ['code' => 200, 'data' => 'done'];
		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error('tender pay callback fail, params:' . json_encode($notify));
			return ['code' => 201, 'data' => $e->getMessage()];
		}
	}


}