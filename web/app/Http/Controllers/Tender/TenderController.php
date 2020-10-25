<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/14
 * Time: 上午10:07
 */

namespace App\Http\Controllers\Tender;


use App\Http\Controllers\Controller;
use App\Model\Member;
use App\Model\Sdt\WxUser;
use App\Model\Slide;
use App\Model\Tender\Tender;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderBroadcastRead;
use App\Model\Tender\TenderCourse;
use App\Model\Tender\TenderFeedback;
use App\Model\Tender\TenderFlow;
use App\Model\Tender\TenderGuess;
use App\Model\Tender\TenderLog;
use App\Model\Tender\TenderMargin;
use App\Model\Tender\TenderMsg;
use App\Model\Tender\TenderOrder;
use App\Model\Tender\TenderWinner;
use App\Model\Tender\TenderWithdraw;
use App\Service\SsoService;
use App\Utils\DateUtil;
use App\Utils\ResUtil;
use App\Utils\TenderConstUtil;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenderController extends Controller
{

	/**
	 * 获取首页数据
	 * @desc index
	 */
	public function index()
	{
		$banners =  Slide::where(['is_show' => 1])
			->where('pos', 1)
			->select(['title', 'link', 'url'])
			->orderBy('sort', 'desc')
			->get();
		// 正在进行
		$tenderings = Tender::tendering();
		$tendertodo = Tender::tendertodo();
		$winners = TenderWinner::where([])->orderByDesc('bonus')->limit(10)->get();
		foreach ($winners as $i => $winner){
			$wx = WxUser::fetchBySsouid($winner->member->uid);
			$winners[$i]->wx = $wx;
		}
		$loginUrl = SsoService::fetchHost() . '/mobile/index.html?appid=5a88e8dedc1ecf7fb04a074bdea376cf&returnurl=' . route('tender');
		return view('front.tender.index', compact('banners', 'tenderings', 'tendertodo', 'winners', 'loginUrl'));
	}

	/**
	 * 获取拍品
	 * @desc detail
	 * @param $id
	 * @return string
	 */
	public function detail($id){
		$model = Tender::where('id', $id)->where('stat', '>', '-1')->first();
		if(empty($model)) {
			return view('errors.404');
		}
		$myguesses = [];
		$mytenders = [];
		if(Auth::guard('front')->user()) {
			$member = Member::current();
			// 我的估价
			$myguesses = TenderGuess::where([
				'tender_id' => $id,
				'member_id' => $member->id
			])->get();
			// 我的出价
			$myTendersQuery = TenderLog::where([
				'tender_id' => $id,
				'member_id' => $member->id
			]);
			$mytenders = $model->isDark() ? $myTendersQuery->first() : $myTendersQuery->get();
		}
		// 最新出价
		$lastTender = $model->lastTender();
		return view('front.tender.detail', compact('model', 'myguesses', 'mytenders', 'lastTender'));
	}

	public function finished($lastId)
	{
		$lastId = intval($lastId);
		$query = Tender::where('tender_end', '<', DateUtil::now());
		if($lastId > 0) {
			$query->where('id', '<', $lastId);
		}
		$query->where('stat', '>', '-1');
		$datas = $query->orderByDesc('id')->limit(10)->get();
		$models = [];
		foreach ($datas as $data) {
			$models[] = [
				'id' => $data->id,
				'banner' => asset('storage/' . $data->banner),
				'name' => $data->name,
				'dealPrice' => $data->dealPrice(),
				'priceCount' => $data->priceCount(),
				'price' => $data->lastPrice(),
				'type' => $data->type
			];
			$lastId = $data->id;
		}
		return ResUtil::ok([
			'models' => $models,
			'lastId' => $lastId,
			'hasMore' => count($models) < 10 ? 0 : 1
		]);
	}

	/**
	 * 用户管理中心
	 * @desc my
	 */
	public function my()
	{
		$member = Member::current();
		$wx = isset($_COOKIE['openid']) ? WxUser::fetchByOpenid($_COOKIE['openid']) : WxUser::fetchBySsouid($member->uid);
		return view('front.tender.my', compact('wx', 'member'));
	}

	/**
	 * 我的竞拍
	 * @desc myauction
	 */
	public function myauction()
	{
		return view('front.tender.myauction');
	}

	public function myauctions($lastId)
	{
		$lastId = intval($lastId);
		$member = Member::current();
		$query = TenderLog::where('member_id', $member->id);
		if($lastId > 0) {
			$query->where('id', '<', $lastId);
		}
		$datas = $query->groupBy('tender_id')->orderByDesc('id')->limit(10)->get();
		$models = [];
		foreach ($datas as $index => $data) {
			$models[$index] = [
				'banner' => asset('storage/' . $data->tender->banner),
				'name' => $data->tender->name,
			];
			$models[$index]['list'] = TenderLog::where([
				'member_id' => $member->id,
				'tender_id' => $data->tender_id
			])->select(['price', 'created_at'])->orderByDesc('price')->get()->toArray();
			$lastId = $data->id;
		}
		return ResUtil::ok([
			'models' => $models,
			'lastId' => $lastId,
			'hasMore' => count($models) < 10 ? 0 : 1
		]);
	}

	/**
	 * 估价
	 * @desc guess
	 * @param Request $request
	 */
	public function guess(Request $request)
	{
		$tender_id = $request->get('tender_id');
		$price = $request->get('price');
		// 拍品是否存在
		$model = Tender::find($tender_id);
		if(empty($model) || $model->stat !== 0) {
			return ResUtil::error(201, '拍品不存在');
		}

		// 是否在估价期
		if(!$model->isGuessing()){
			return ResUtil::error(202, '拍品不在估价时段');
		}

		$member = Member::current();
		try{
			// 钱是否够
			\DB::beginTransaction();
			$tenderAsset = TenderAsset::where('member_id', $member->id)->first();
			if(empty($tenderAsset) || $tenderAsset->amount < TenderConstUtil::guessPrice()) {
				throw new \Exception("小红花不足，<a href='/tender/recharge'>前往充值</a>");
			}

			if(!TenderGuess::add($tender_id, $price, $member->id)){
				throw new \Exception('网络异常，请稍后再试');
			}

			$tenderAsset->amount -= TenderConstUtil::guessPrice();
			if(!$tenderAsset->save()) {
				throw new \Exception('网络异常，请稍后再试');
			}

			$model->guess_count++;
			if(!$model->save()) {
				throw new \Exception('网络异常，请稍后再试');
			}

			TenderFlow::create([
				'member_id' => $member->id,
				'amount' => -1 * TenderConstUtil::guessPrice(),
				'type' => TenderFlow::TYPE_GUESS,
				'after_amount' => $tenderAsset->amount
			]);
			DB::commit();
			return ResUtil::ok();

		}catch (\Exception $e){
			DB::rollBack();
			return ResUtil::error(201, $e->getMessage());
		}
	}

	/**
	 * 暗标或者竞拍
	 * @desc tender
	 */
	public function tender(Request $request)
	{
		$tender_id = $request->get('tender_id');
		$price = $request->get('price');

		if(!is_numeric($tender_id) || !is_numeric($price)) {
			return ResUtil::error(201, '请求异常');
		}

		$member = Member::current();
		$broadcast_read = TenderBroadcastRead::where('member_id', $member->id)
			->where('broad_id', 1)->first();
		if(empty($broadcast_read)) {
			return ResUtil::error(210, "请先，<a href='/tender/contract'>阅读拍卖合同</a>");
		}

		// 拍品是否存在
		$model = Tender::find($tender_id);
		if(empty($model) || $model->stat !== 0) {
			return ResUtil::error(201, '拍品不存在');
		}

		// 是否在估价期
		if(!$model->isTendering()){
			return ResUtil::error(202, '拍品不在竞拍时段');
		}

		try{
			\DB::beginTransaction();

			// 是否缴纳保证金
			if (!TenderMargin::exist($member->id)) {
				DB::rollBack();
				return ResUtil::error(208, "请先，<a href='/tender/margin'>缴纳保证金</a>");
			}

			if ($model->isDarkAndTenderd($member->id)){
				// 如果是暗标，只能出一次价
				throw new \Exception("您已出过价，暗标产品只能出价一次");
			}

			// 如果是竞拍，必须大于最高价和起拍价
			$maxPrice = $model->maxPrice();
			if ($price <= $model->maxPrice()) {

				throw new \Exception("您的出价必须高于最新价：" . $maxPrice);
			}

			// 写数据
			TenderLog::create([
				'member_id' => $member->id,
				'tender_id' => $tender_id,
				'price' => $price
			]);

			DB::commit();
			return ResUtil::ok();

		}catch (\Exception $e){
			DB::rollBack();
			return ResUtil::error(201, $e->getMessage());
		}
	}

	public function margin(Request $request)
	{
		if($request->isMethod('GET')){
			$member = Member::current();
			$margined = TenderMargin::exist($member->id);
			return view('front.tender.margin', compact('margined'));
		}else {
			$cmd = $request->get('cmd');
			if (!in_array($cmd, ['back', 'pay'])) {
				return ResUtil::error(201, '请求异常');
			}

			try{
				DB::beginTransaction();
				$member = Member::current();
				if($cmd == 'back') {
					// 退还保证金
					// 是否有缴纳过？
					$tenderMargin = TenderMargin::where('member_id', $member->id)->first();
					if(!$tenderMargin) {
						throw new \Exception('您尚未缴纳过保证金');
					}

					// 是否有未结束的参拍品
					$tender_logs = DB::select("select count(1) as c from tender_logs inner join tenders on tender_logs.tender_id = tenders.id where tender_logs.member_id = ? and tenders.stat = ?", [$member->id, Tender::STAT_DONE]);
					if (!empty($tender_logs) && $tender_logs[0]->c > 0) {
						throw new \Exception('您所参拍作品尚未拍卖结束，暂不可退还保证金');
					}

					// 退还保证金
					if (!DB::statement('delete from tender_margin where member_id = ?', [$member->id])) {
						throw new \Exception('退还保证金失败，请稍后再试');
					}

					// 增加小红花
					TenderAsset::add($member->id, $tenderMargin->amount, TenderFlow::TYPE_MARGIN_BACK);

					DB::commit();
					return ResUtil::ok('保证金退还成功，可前往查看小红花流水');

				}else {
					// 缴纳保证金
					// 是否有缴纳过？
					if(TenderMargin::exist($member->id)) {
						throw new \Exception('您已缴纳过保证金');
					}

					$cost = 500 * TenderConstUtil::PARITY;

					// 钱是否足够
					$tenderAsset = TenderAsset::where('member_id', $member->id)->first();
					if(!$tenderAsset || $tenderAsset->amount < $cost){
						DB::rollBack();
						return ResUtil::error(209, "小红花不足，<a href='/tender/recharge'>前往充值</a>");
					}

					// 添加保证金数据
					TenderMargin::create([
						'member_id' => $member->id,
						'amount' => $cost
					]);
					//扣除小红花
					TenderAsset::add($member->id, -1 * $cost, TenderFlow::TYPE_MARGIN_PAY);
					DB::commit();
					return ResUtil::ok('保证金缴纳成功');
				}
			} catch (\Exception $e){
				DB::rollBack();
				return ResUtil::error(202, $e->getMessage());
			}
		}
	}

	public function recharge()
	{
		return view('front.tender.recharge');
	}

	public function prepay(Request $request)
	{
		if(!is_weixin() || empty($_COOKIE['openid'])) {
			return ['code'=>201, 'data'=>'请在微信公众号中登录'];
		}
		$amount = $request->get('amount', 0.00);
		if($amount <= 0) {
			return ['code'=>201, 'data'=>'请输入正确的金额'];
		}
		$order_no = wx_order_no();
		// 获取open_id
		$open_id = $_COOKIE['openid'];
		$member = Member::current();
		$options = config('wechat');
		// 生成预订单
		$app = new Application($options);
		$payment = $app->payment;
		$attributes = [
			'trade_type'       => 'JSAPI',
			'body'             => '艺奖堂用户购买小红花'.$amount.'个',
			'detail'           => '购买小红花',
			'out_trade_no'     => $order_no,
			'notify_url'       => 'https://server.yigongpan.com/pay/callback',
			'openid'           => $open_id,
			'total_fee'        => $amount * 100, // 微信是以分为单位
			'limit_pay'        => 'no_credit'
		];
		$order = new Order($attributes);
		$result = $payment->prepare($order);
		if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
			$prepayId = $result->prepay_id;
		} else {
			return ['code'=>201, 'data'=>'预支付失败'];
		}
		$data = [
			'order_id'=>$order_no,
			'member_id'=>$member->id,
			'stat'=>0,
			'type'=>1,
			'amount'=>$amount,
		];
		// 创建订单记录
		TenderOrder::create($data);
		$config = $payment->configForJSSDKPayment($prepayId);
		//$config = $payment->configForPayment($prepayId  );
		\Log::debug('tender_order data : ' . json_encode($data));
		\Log::debug('config for wx : ' . json_encode($config));

		return ['code'=>200, 'data'=>$config];
	}

	public function about()
	{
		return view('front.tender.about');
	}

	public function myguess()
	{
		return view('front.tender.myguess');
	}

	public function myguesses($lastId)
	{
		$lastId = intval($lastId);
		$member = Member::current();
		$query = TenderGuess::where('member_id', $member->id);
		if($lastId > 0) {
			$query->where('id', '<', $lastId);
		}
		$datas = $query->groupBy('tender_id')->orderByDesc('id')->limit(10)->get();
		$models = [];
		foreach ($datas as $index => $data) {
			$models[$index] = [
				'banner' => asset('storage/' . $data->tender->banner),
				'name' => $data->tender->name,
			];
			$models[$index]['list'] = TenderGuess::where([
				'member_id' => $member->id,
				'tender_id' => $data->tender_id
			])->select(['tender_price', 'created_at'])->orderByDesc('tender_price')->get()->toArray();
			$lastId = $data->id;
		}
		return ResUtil::ok([
			'models' => $models,
			'lastId' => $lastId,
			'hasMore' => count($models) < 10 ? 0 : 1
		]);
	}


	public function mybill()
	{
		return view('front.tender.mybill');
	}

	public function mybills($lastId)
	{
		$lastId = intval($lastId);
		$member = Member::current();
		$query = TenderFlow::where('member_id', $member->id);
		if($lastId > 0) {
			$query->where('id', '<', $lastId);
		}
		$datas = $query->orderByDesc('id')->limit(10)->get();
		$models = [];
		foreach ($datas as $index => $data) {
			$models[] = [
				'desc' => $data->defCon(),
				'amount' => $data->amount,
				'created_at' => strval($data->created_at)
			];
			$lastId = $data->id;
		}

		return ResUtil::ok([
			'models' => $models,
			'lastId' => $lastId,
			'hasMore' => count($models) < 10 ? 0 : 1
		]);
	}

	public function withdraw(Request $request)
	{
		if ($request->isMethod('GET')) {
			$member = Member::current();
			// 已提现金
			$withrawed = TenderWithdraw::acceptedAmount($member->id);
			// 可提现金
			$canWithdraw = TenderAsset::canWithdraw($member);

			return view('front.tender.withdraw', compact('member', 'withrawed', 'canWithdraw'));
		}else {
			$amount = $request->get('amount');
			$card = $request->get('card');
			$name = $request->get('name');
			$bank = $request->get('bank');
			if (!is_numeric($amount) || empty($card) || empty($name) || empty($bank)) {
				return ResUtil::error(201, '请求异常');
			}
			DB::beginTransaction();
			try{
				// 看看有多少钱
				$member = Member::current();
				$tenderAsset = 	$member->tender_asset;
				$canWithdraw = TenderAsset::canWithdraw($member);
				if(empty($tenderAsset) || $amount > $canWithdraw) {
					throw new \Exception('金额不足');
				}

				$huaAmount = $amount * TenderConstUtil::PARITY;

				$tenderAsset->amount -= $huaAmount;
				if(!$tenderAsset->save()) {
					throw new \Exception('数据库写入失败');
				}

				TenderFlow::create([
					'member_id' => $member->id,
					'amount' => -1 * $huaAmount,
					'type' => TenderFlow::TYPE_WITHDRAW,
					'after_amount' => $tenderAsset->amount
				]);

				TenderWithdraw::create([
					'member_id' => $member->id,
					'amount' => $amount,
					'stat' => TenderWithdraw::STAT_INIT,
					'card' => $card,
					'name' => $name,
					'bank' => $bank
				]);

				DB::commit();
				return ResUtil::ok('提交成功，平台将在两个工作日内审核打款');

			}catch (\Exception $e){
				DB::rollBack();
				return ResUtil::error(201, $e->getMessage());
			}
		}
	}

	public function logout()
	{
		Auth::guard('front')->logout();
		$url = SsoService::fetchHost() . '/mobile/index.html?appid=5a88e8dedc1ecf7fb04a074bdea376cf&returnurl=' . route('tender');
		header('Location: ' . $url);
		header("HTTP/1.0 302 Found");
		exit;
	}

	public function unReadMsgCount()
	{
		$member = Member::current();
		$count = TenderMsg::unReadCount($member->id);
		return ResUtil::ok($count);
	}

	public function mymsgs(Request $request)
	{
		if($request->ajax()) {
			$lastId = $request->get('lastId');
			$member = Member::current();
			$query = TenderMsg::where('member_id', $member->id);
			if($lastId > 0) {
				$query->where('id', '<', $lastId);
			}
			$models = $query->orderByDesc('id')->limit(10)->get();
			$ret = ['models' => [], 'lastId' => $lastId, 'hasMore' => 0];
			$count = count($models);
			if($count > 0){
				foreach ($models as $i => $model) {
					$models[$i]->con = $model->temp();
				}
				$ret = [
					'models' => $models,
					'lastId' => $models[$count-1]->id,
					'hasMore' => $count < 10 ? 0 : 1
				];
			}

			return ResUtil::ok($ret);

		}else{
			$member = Member::current();
			$models = TenderMsg::where('member_id', $member->id)
				->orderByDesc('id')
				->limit(10)->get();
			$ret = ['models' => [], 'lastId' => 0, 'hasMore' => 0];
			$count = count($models);
			if($count > 0){
				$ret = [
					'models' => $models,
					'lastId' => $models[$count-1]->id,
					'hasMore' => $count < 10 ? 0 : 1
				];
			}
			return view('front.tender.mymsgs', $ret);
		}
	}

	public function msgRead($id)
	{
		$member = Member::current();
		$id = intval($id);
		if(empty($id)) {
			return ResUtil::error();
		}

		$model = TenderMsg::where([
			'id' => $id,
			'member_id' => $member->id,
		])->first();
		if(empty($model)) {
			return ResUtil::error();
		}
		$model->has_read = 1;
		$model->save();
		return ResUtil::ok();
	}


	public function aboutMe()
	{
		return view('front.tender.aboutme');
	}

	public function rule()
	{
		return view('front.tender.rule');
	}

	public function fqa()
	{
		return view('front.tender.fqa');
	}

	public function feedback(Request $request)
	{
		if($request->isMethod('GET')){
			return view('front.tender.feedback');
		}else {
			$con = $request->get('con');
			$member = Member::current();
			TenderFeedback::create([
				'member_id' => $member->id,
				'con' => $con
			]);
			return ResUtil::ok();
		}
	}

	public function course()
	{
		$models = TenderCourse::where('stat', 1)
			->orderByDesc('created_at')
			->limit(10)
			->get();
		return view('front.tender.course', compact('models'));
	}

	public function courseDetail($id)
	{
		$model = TenderCourse::find($id);
		return view('front.tender.courseDetail', compact('model'));
	}
}