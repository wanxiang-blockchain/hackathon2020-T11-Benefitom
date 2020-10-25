<?php

namespace App\Http\Controllers\Front;

use App\Model\Account;
use App\Model\Artbc;
use App\Model\Asset;
use App\Model\Delivery;
use App\Model\Member;
use App\Model\Project;
use App\Http\Controllers\Controller;
use App\Model\ProjectOrder;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderFlow;
use App\Model\ArtbcLog;
use App\Service\AccountService;
use App\Service\MemberService;
use App\Service\SubscriptionService;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;
    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * SubscriptionController constructor.
     */
    public function __construct(SubscriptionService $subscriptionService, AccountService $accountService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->accountService = $accountService;
    }


    /**
	 * 认购首页
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$data = Project::where(['is_show' => 1])
		->orderBy('asset_code')
		->paginate(10);
		return view('front.subscription.index', ['data'=>$data]);
    }

	/**
	 * 认购详情
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function detail($id, AccountService $accountService)
	{
	    $project = Project::findOrFail($id);
//	    $project_orders = ProjectOrder::where("project_id",$id)
//		    ->limit(10)
//		    ->orderBy('created_at', 'desc')
//		    ->get();
//		$project_num = ProjectOrder::where("project_id",$id)->count();
		$member_id = 0;
		if(\Auth::guard('front')->check()) {
		    $member_id = \Auth::guard('front')->id();
            $finance = $accountService->balance($member_id);
        } else {
            $finance = 0;
        }
        $sub_num =request()->get('sub_num', 1);
		if(($project->limit - $project->sold()) < $sub_num) {
		    $sub_num = $project->limit - $project->sold();
        }
        $is_weixin = is_weixin();
		$check_money = number_format($project->price * $sub_num, 2, '.', '');
        $key = trim(file_get_contents("../rsa_1024_pub.pem"));
        // 地址
		$addr = Artbc\Addr::fetchByMemberId($member_id);
		return view('front.subscription.detail', compact("project",'finance', 'key',
			'sub_num', 'check_money', 'is_weixin', 'addr'));
	}

	public function need() {
        $request = request();
	    $accountService = new AccountService();
	    $project = Project::findOrFail($request->get('id'));
        $id = \Auth::guard('front')->id();
        $balance      = $accountService->balance($id);
        $amount       = $request->input('amount');
        if (intval($amount) <= 0) throw new Exception("Input is invalid");
        $price        = $project->price;
        $total_amount = $price * $amount;

        $need         = $total_amount - $balance;
        $need  = $need <= 0 ? 0 : $need;
        $trade_pwd =  Account::where(['id'=>$accountService->getAccountId($id)])->first()->trade_pwd;
        $is_trade_pwd = $trade_pwd ? 1 : 0;
        $detail = [
            "picture"      => $project->picture,
            "amount"       => $amount,
            "price"        => $price,
            "total_amount" => $total_amount,
            'need'         => $need,
            'balance'      => $balance,
            'name'         => $project->name,
            'id'           => $project->id,
            'is_trade_pwd'    => $is_trade_pwd,
            'key'   => trim(file_get_contents("../rsa_1024_pub.pem"))
        ];
        // 存进session?
        session()->put('detail', $detail);

        return 'success';
    }

    public function qcashpay(Request $request, AccountService $accountService) {
        $this->need();
        $txid = $request->get('txid', '');
        if (empty($txid)) {
            return ['code'=>505, 'data'=>'交易失败'];
        }
        \DB::beginTransaction();
        $account = Account::where(['member_id'=>request()->user('front')->id])->sharedLock()->first();
        if(!$account->trade_pwd) {
//            return ['code'=>505, 'data'=>'您还没有设置交易密码,请前往用户管理中心>账户设置进行设置'];
        }

        // 如果从session里取不出detail值，则为重复提交？？？
        $detail = session()->get('detail',null);

        $project_id     = $detail['id'];
        $member = Member::current();
        $member_id      = $member->id;
        $total_amount   = $detail['amount'];
        $price          = $detail["price"];
        try{
            $project = Project::find($project_id);
            if(empty($project)){
                throw new \Exception('认购藏品不存在');
            }

            $addr = Artbc\Addr::fetchByMemberId($member->id);
            if (!$addr) {
                throw new \Exception('请先设置收货地址');
            }

            $order = $this->subscriptionService->makeOrder($project_id, $member_id, $total_amount, $price);
            $this->subscriptionService->payOrder($order->id);
            // 购买成功后，赠送小红花
            $bi_amount = round($total_amount * $price / Artbc\BtConfig::getPrice(), 5);
            // 购买成功后，赠送artbc
            ArtbcLog::add($member_id, $bi_amount);
            // 分级奖励
            MemberService::dis($member, $bi_amount);

            // TODO 购买后直接入提货
            $data = $addr->toArray();
            $data['amount'] = $total_amount;
            $data['member_id'] = $member_id;
            $data['asset_code'] = $project->asset_code;
            // 记录提货信息
            if (!Delivery::create($data)) {
                throw new \Exception('服务器异常，请稍等再试');
            }
            // 扣除提货后资产数量
            $accountService->addAsset($account->id, $project->asset_code, -1 * $data['amount']);
            \DB::commit();
            return ['code'=>200, 'data'=>'认购成功'];
        }catch(\Exception $e){
            \Log::error($e->getTraceAsString());
            return ['code'=>505, 'data'=>$e->getMessage()];
        }

    }



    public function pay(Request $request, AccountService $accountService) {
	    $this->need();
        \DB::beginTransaction();
        if (openssl_private_decrypt(base64_decode($request->get('trade_pwd')),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $trade_pwd = $decrypted;
        } else {
            die();
        }
        if(!$trade_pwd) {
            return ['code'=>505, 'data'=>'请输入正确的交易密码'];
        }
        $account = Account::where(['member_id'=>request()->user('front')->id])->sharedLock()->first();
        if(!$account->trade_pwd) {
            return ['code'=>505, 'data'=>'您还没有设置交易密码,请前往用户管理中心>账户设置进行设置'];
        }
        if(!\Hash::check($trade_pwd, $account->trade_pwd)) {
            return ['code'=>505, 'data'=>'请输入正确的交易密码'];
        }

        // 如果从session里取不出detail值，则为重复提交？？？
        $detail = session()->get('detail',null);
        if(!$detail) return ['code'=>505, 'data'=>'数据已经提交请不要重复提交'];

        if ($detail['need'] == 0) {
            $project_id     = $detail['id'];
            $member = Member::current();
            $member_id      = $member->id;
            $total_amount   = $detail['amount'];
            $price          = $detail["price"];
	        try{
		        $project = Project::find($project_id);
		        if(empty($project)){
					throw new \Exception('认购藏品不存在');
		        }

		        $addr = Artbc\Addr::fetchByMemberId($member->id);
		        if (!$addr) {
			        throw new \Exception('请先设置收货地址');
		        }

		        $order = $this->subscriptionService->makeOrder($project_id, $member_id, $total_amount, $price);
		        $this->subscriptionService->payOrder($order->id);
		        // 购买成功后，赠送小红花
//		        TenderAsset::add($member_id, $project->tender_prize * $total_amount, TenderFlow::TYPE_GONGPAN_PRIZE);
		        $bi_amount = round($total_amount * $price / Artbc\BtConfig::getPrice(), 5);
		        // 购买成功后，赠送artbc
		        ArtbcLog::add($member_id, $bi_amount);
		        // 分级奖励
		        MemberService::dis($member, $bi_amount);

		        // TODO 购买后直接入提货
		        $data = $addr->toArray();
		        $data['amount'] = $total_amount;
		        $data['member_id'] = $member_id;
		        $data['asset_code'] = $project->asset_code;
		        // 记录提货信息
		        if (!Delivery::create($data)) {
			        throw new \Exception('服务器异常，请稍等再试');
		        }
		        // 扣除提货后资产数量
		        $accountService->addAsset($account->id, $project->asset_code, -1 * $data['amount']);
		        \DB::commit();
		        return ['code'=>200, 'data'=>'认购成功，获赠' . $bi_amount . '枚artbc'];
	        }catch(\Exception $e){
            	\Log::error($e->getTraceAsString());
                return ['code'=>505, 'data'=>$e->getMessage()];
            }
        }

    }

    public function subSuccess()
    {
        return view('front.subscription.subSuccess');
    }

	/**
	 * 确认认购详情页
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function checkBefore($id)
	{
		$project = Project::with('articles')->find($id)->toArray();
		$cash = [];//todo::用户资金表
		$data = compact('project', 'cash');
		dd($data);
		return view('front.subscription_check_before', $data);
	}
}
