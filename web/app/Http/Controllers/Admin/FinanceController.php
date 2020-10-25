<?php

namespace App\Http\Controllers\Admin;

use App\Model\AlipayLogs;
use App\Model\Asset;
use App\Model\AssetType;
use App\Model\Account;
use App\Model\Delivery;
use App\Model\Finance;
use App\Model\FinanceType;
use App\Model\Member;
use App\Model\OpeLog;
use App\Model\Project;
use App\Model\RechargeAudit;
use App\Model\AccountFlow;
use App\Model\Score;
use App\Model\ScoreLog;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderFlow;
use App\Model\TradeLog;
use App\Model\User;
use App\Model\WithDraw;
use App\Service\AliPayService;
use App\Service\ValidatorService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\AccountService;
use App\Service\FinanceService;
use Hash;
use Carbon\Carbon;
use DB;

class FinanceController extends Controller
{
    function __construct(AccountService $accountService,FinanceService $financeService)
    {
        $this->accountService = $accountService;
        $this->financeService = $financeService;
    }

    public function alilog(Request $request)
    {
    	$logs = AlipayLogs::rightjoin('members', 'alipay_logs.member_id', '=', 'members.id')
	    ->where(function($query) use ($request) {
		    $phone = $request->get('phone');
		    $beginTime = request()->get('beginTime');
		    $endTime = request()->get('endTime');
		    $end = date('Y-m-d');
		    if($phone){
			    $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
			    $query->whereIn('member_id',$member);
		    }
		    if($beginTime) {
			    $_endTime = $endTime ? $endTime : $end;
			    $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
			    $query->whereBetween('alipay_logs.created_at', [$beginTime, $_endTime]);
		    }
	    })->orderBy('alipay_logs.id', 'desc')
		    ->select('alipay_logs.*', 'members.phone')
		    ->paginate(10);
	    foreach ($logs as $i => $log) {
		    if(empty($log['content'])) {
		    	$logs[$i]['gmt_create'] = $log['created_at'];
		    	$logs[$i]['buyer_logon_id'] = '无';
		    	continue;
		    }
		    $con = json_decode($log['content'], true);
		    $logs[$i]['gmt_create'] = $con['gmt_create'];
		    $logs[$i]['buyer_logon_id'] = $con['buyer_logon_id'];
    	}
	    return view('admin.finance.alipay_log', compact('logs'));
    }


    public function recharge(Request $request)
	{
        $recharge = Finance::leftjoin('finance_types','finances.type','=','finance_types.code')
        ->where(function($query)use($request) {
            $asset_type = $request->get('asset_type');
            $phone = $request->get('phone');
            $type = $request->get('type');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($asset_type){
                $query->where('asset_type','=',$asset_type);
            }
            if($phone){
                $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                $query->whereIn('member_id',$member);
            }
            if($type){
                $query->where('type',$type);
            }else{
                $query->whereIn('type',[1,2]);
            }
            if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('finances.created_at', [$beginTime, $_endTime]);
            }
        })->orderBy('finances.id','desc')
            ->select('finances.*','finance_types.name')
            ->paginate(10);

        $balance_sum = Finance::leftjoin('finance_types','finances.type','=','finance_types.code')
            ->where(function($query)use($request) {
                $asset_type = $request->get('asset_type');
                $phone = $request->get('phone');
                $type = $request->get('type');
                $beginTime = request()->get('beginTime');
                $endTime = request()->get('endTime');
                $end = date('Y-m-d');
                if($asset_type){
                    $query->where('asset_type','=',$asset_type);
                }
                if($phone){
                    $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                    $query->whereIn('member_id',$member);
                }
                if($type){
                    $query->where('type',$type);
                }else{
                    $query->whereIn('type',[1,2]);
                }
                if($beginTime) {
                    $_endTime = $endTime ? $endTime : $end;
                    $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                    $query->whereBetween('finances.created_at', [$beginTime, $_endTime]);
                }
            })->orderBy('finances.id','desc')
            ->select('finances.*','finance_types.name')
            ->sum('balance');
        $page_sum = empty($recharge->toarray()) ? "" : array_sum(array_column($recharge->toarray()['data'],'balance'));
        $recharge->appends($request->all());
        $assetTypes = AssetType::get();
        $data = compact('assetTypes','recharge','page_sum','balance_sum');
       return view('admin.finance.recharge',$data);
    }


    public function addRecharge(Request $request, ValidatorService $validatorService){
        if($request->method() == 'GET'){
            $asset_type = AssetType::all()->map(function($v) {
                return [
                    "name"  => $v["name"],
                    "value" => $v['code'],
                ];
            });
            $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
            return view('admin.finance.addRecharge',['values' => $asset_type, 'key'=>$key]);
        }else{
            $data = $request->all();
            if(openssl_private_decrypt(base64_decode($data['password']), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
                $data['password'] = $decrypted;
            }
            $valite = $validatorService->checkValidator([
                'phone'    => 'required',
                'price' => 'required',
                'password' => 'required',
                'time'      => 'date'
            ], $data);
            if($valite['code'] != 200) {
                return $valite;
            }
            if($data['asset_type'] !='T000000001' && $data['balance'] == ""){
                return ['code'=>230,'data'=>'充值藏品时单价不能为空'];
            }
            if(Hash::check($data['password'], Auth()->user()->password)){
                $member_id = Member::where('phone',$data['phone'])->pluck('id')->first();
                if($member_id){
                    $time = $request->get('time');
                    $time = $time > Carbon::now()->toDateTimeString() ? $time : '';
                    $amount = 0;
                    $balance =0;
                    $data['asset_type'] == 'T000000001' ? ($balance = $data['price']) : ($amount = $data['price']);
                    if(!empty($time)){
                        $content = "管理员充值".$data['price'].",解冻时间为".$time;
                    }else{
                        $content = "管理员充值".$data['price'];
                    }
                    if($data['asset_type'] == 'T000000001'){
                        RechargeAudit::create([
                            "member_id"  => $member_id,
                            "amount"     => $balance,
                            "asset_type" => $data['asset_type'],
                            "content"    => $content,
                            "unlock_time"=> $time,
                            "status"     => 1
                        ]);
                    }else{
                        RechargeAudit::create([
                            "member_id"  => $member_id,
                            "balance"    => $data['balance'],
                            "amount"     => $amount,
                            "asset_type" => $data['asset_type'],
                            "content"    => $content,
                            "unlock_time"=> $time,
                            "status"     => 1
                        ]);
                    }
                    return ['code'=>200, 'data'=>'提交成功，待审核'];

                }else{
                    return ['code'=>201, 'data'=>'充值会员不存在'];
                }
            }else{
                return ['code'=>203, 'data'=>'密码错误'];
            }

        }
    }
    public function audit_list(Request $request){
        $audit = RechargeAudit::leftjoin('asset_types','recharge_audits.asset_type','=','asset_types.code')
            ->leftjoin('members','recharge_audits.member_id','=','members.id')
            ->leftjoin('users','recharge_audits.audit_id','=','users.id')
            ->where(function($query)use($request) {
                $asset_type = $request->get('asset_type');
                $phone = $request->get('phone');
                $beginTime = request()->get('beginTime');
                $endTime = request()->get('endTime');
                $end = date('Y-m-d');
                $status = $request->get('status');
                if($status){
                    $query->where('status',$status);
                }
                if($asset_type){
                    $query->where('asset_type','=',$asset_type);
                }
                if($phone){
                    $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                    $query->whereIn('member_id',$member);
                }
                if($beginTime) {
                    $_endTime = $endTime ? $endTime : $end;
                    $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                    $query->whereBetween('recharge_audits.created_at', [$beginTime, $_endTime]);
                }
            })->orderBy('recharge_audits.id','desc')
            ->select('recharge_audits.*','asset_types.name','members.phone','users.name as uname')
            ->paginate(10);
        $audit->appends($request->all());
        $assetTypes = AssetType::get();
        $role_type = \Auth::guard('web')->user()->role_type;
        return view('admin.finance.recharge_audit',compact('audit','assetTypes','role_type'));
    }
    public function audit(){
        $id = Request()->get('id');
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if($user['role_type'] != 3){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $audit = RechargeAudit::find($id);
        if(!empty($audit)){
            if($audit['status'] == 1){
                DB::beginTransaction();
	            try{

		            $account_id = $this->accountService->getAccountId($audit['member_id']);
		            $time = $audit['unlock_time'];
		            // 取出原资产持有，TODO 计算成本价是否需要加入锁定资产？
		            $ori_asset = Asset::fetchAssetData($account_id, $audit['asset_type']);
		            $this->accountService->addAsset($account_id, $audit['asset_type'],$audit['amount'], $time > Carbon::now()->toDateTimeString() ? $time : '');
		            if($time>Carbon::now()->toDateTimeString()){
			            $content = "管理员充值".$audit['amount'].",解冻时间为".$time;
		            }else{
			            $content = "管理员充值".$audit['amount'];
		            }
		            $account_flow = new AccountFlow();
		            if($audit['asset_type'] == 'T000000001'){
			            $this->financeService->adminRecharge($audit['member_id'], $audit['asset_type'],1,$audit['amount'],0 ,$content);
			            $account_flow->create_log($audit['member_id'],$audit['amount'],$audit['amount'],$content);
		            }else{
			            $this->financeService->adminRecharge($audit['member_id'], $audit['asset_type'],1,$audit['balance'] * $audit['amount'],$audit['amount'] ,$content);
			            $account_flow->create_log($audit['member_id'],$audit['balance'] * $audit['amount'],$audit['balance'] * $audit['amount'],$content);
			            $this->accountService->buyCost($ori_asset['cost'], $ori_asset['amount'], $audit['balance'], $audit['amount'], $account_id, $audit['asset_type']);
			            // 赠送小红花
			            $project = Project::where('asset_code', $audit['asset_type'])->first();
			            if($project && $project->tender_prize > 0){
			            	TenderAsset::add($audit['member_id'], $project->tender_prize * $audit['amount'], TenderFlow::TYPE_GONGPAN_PRIZE);
			            }
		            }
		            $audit -> audit_id = $user_id;
		            $audit ->status = 3;
		            $audit ->save();


		            DB::commit();
		            return ['code'=>200, 'data'=>'审核成功'];
	            }catch (\Exception $e){
                	DB::rollBack();
                	\Log::error($e->getTraceAsString());
                	return ['code' => 500, $e->getMessage()];
                }
            }else{
                return ['code'=>202,'data'=>'请勿重复操作'];
            }
        }else{
            return ['code'=>201,'data'=>' 记录不存在'];
       }
    }
    public function recharge_reject(Request $request){
        $user_id = Auth()->id();
        $user = User::find($user_id);
	    if(!in_array($user['role_type'], [1, 3])) {
		    return ['code'=>201, 'message'=>'无权限操作'];
	    }
        $id = $request->get('id');
        $reason = $request->get('reason');
        $audit = RechargeAudit::find($id);
        if(!empty($audit)){
            if($audit['status'] == 1){
                DB::beginTransaction();
                $user_id = Auth()->id();
                $audit->audit_id = $user_id;
                $audit->audit_reason = $reason;
                $audit->status = 2;
                $audit->save();
                DB::commit();
                return ['code'=>200];
            }
            return ['code'=>201,'message'=>'请勿重复操作'];
        }
           return ['code'=>201,'message'=>'记录不存在'];

    }
    public function log(Request $request){

        $finance = Finance::where(function($query)use($request) {
            $type = $request->get('type');
            $phone = $request->get('phone');
            $asset_type = $request->get('asset_type');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($type){
                $query->where('type','=',$type);
            }
            if($asset_type){
                $query->where('asset_type','=',$asset_type);
            }
            if($phone){
                $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                $query->whereIn('member_id',$member);
            }
            if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('created_at', [$beginTime, $_endTime]);
            }

        })->orderBy('id','desc')->paginate(10);

        $f_sum = Finance::where(function($query)use($request) {
            $type = $request->get('type');
            $phone = $request->get('phone');
            $asset_type = $request->get('asset_type');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($type){
                $query->where('type','=',$type);
            }
            if($asset_type){
                $query->where('asset_type','=',$asset_type);
            }
            if($phone){
                $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                $query->whereIn('member_id',$member);
            }
            if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('created_at', [$beginTime, $_endTime]);
            }

        })->orderBy('id','desc')->sum('balance');
        $page_sum = empty($finance->toarray()) ? "" : round(array_sum(array_column($finance->toarray()['data'],'balance')),2);
        $finance->appends($request->all());
        $finance_types = FinanceType::get();
        $assetTypes = AssetType::get();
        return view('admin/finance/log',compact('finance','finance_types','assetTypes','page_sum','f_sum'));
    }

    public function memberRecharge(Request $request){
        $finance = Finance::where(function($query)use($request) {
            $phone = $request->get('phone');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($phone){
                $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                $query->whereIn('member_id',$member);
            }
            if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('created_at', [$beginTime, $_endTime]);
            }

        })->where('type' ,2)->orderBy('id','desc')->paginate(10);
        $finance->appends($request->all());
        return view('admin/finance/memberRecharge',compact('finance','finance_types'));
    }

    public function withdraw(Request $request){

        $withdraw = WithDraw::leftjoin('members','with_draws.member_id','=','members.id')
        ->where(function($query)use($request) {
            $status = $request->get('status');
            $phone = $request->get('phone');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($status){
                $query->where('status','=',$status);
            }
            if($phone){
                $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                $query->whereIn('member_id',$member);
            }
            if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('with_draws.created_at', [$beginTime, $_endTime]);
            }


        })->orderBy('with_draws.id','desc')->select('with_draws.*','members.phone')->paginate(10);

        $withdraw_sum = WithDraw::leftjoin('members','with_draws.member_id','=','members.id')
            ->where(function($query)use($request) {
                $status = $request->get('status');
                $phone = $request->get('phone');
                $beginTime = request()->get('beginTime');
                $endTime = request()->get('endTime');
                $end = date('Y-m-d');
                if($status){
                    $query->where('status','=',$status);
                }
                if($phone){
                    $member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
                    $query->whereIn('member_id',$member);
                }
                if($beginTime) {
                    $_endTime = $endTime ? $endTime : $end;
                    $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                    $query->whereBetween('with_draws.created_at', [$beginTime, $_endTime]);
                }

            })->sum('money');
        $page_sum = empty($withdraw->toarray()) ? "" : array_sum(array_column($withdraw->toarray()['data'],'money'));
        $withdraw->appends($request->all());
        $role_type = \Auth::guard('web')->user()->role_type;
        return view('admin.finance.withdraw',compact('withdraw','page_sum','withdraw_sum','role_type'));
    }
    public function reject(Request $request){
        $id = $request->get('id');
	    if(!in_array(\Auth::guard('web')->user()->role_type, [1, 3])) {
		    return ['code'=>201, 'message'=>'无权限操作'];
	    }
        $reason = $request->get('reason');
        $withdraw = WithDraw::where('id',$id)->first();
        if($withdraw['status'] == 1){
            try{
                DB::beginTransaction();
                $account_id = $this->accountService->getAccountId($withdraw['member_id']);
                if(empty($account_id)){
                    return ['code'=>205, 'message'=>'驳回失败'];
                }
                $this->accountService->addAsset($account_id, Account::BALANCE, $withdraw['money']);
	            $f = FinanceService::record($withdraw['member_id'], Account::BALANCE, Finance::WITHDRAW, $withdraw['money'],
		            0, '提现驳回到余额:'.$request->get('payment').$withdraw['money'].'元');
	            if(!$f) {
	            	throw new \Exception('驳回失败');
	            }
                $withdraw->reason = $reason;
                $withdraw->reject_time = time();
                $withdraw->status = 2;
                $withdraw->save();
                DB::commit();
                return ['code'=>200];
            }catch (\Exception $e){
            	DB::rollBack();
                return ['code'=>204,'message'=>'驳回失败'];
            }
        }else{
            return ['code'=>201,'message'=>'请不要重复提交'];
        }

    }
    //提现审核通过
    public function adopt(Request $request, AliPayService $aliPayService){
        $id = $request->get('id');
        $withdraw = WithDraw::find($id);
        if(!$withdraw) {
            return ['code'=>201, 'message'=>'fail'];
        }
        if(!in_array(\Auth::guard('web')->user()->role_type, [1, 3])) {
            return ['code'=>201, 'message'=>'无权限操作'];
        }
        if($withdraw['status'] == 1){
            $withdraw->status = 3;
            $withdraw->save();
            $content = '提现审核通过';
            $account_flow = new AccountFlow();
            $account_id = $account_flow->create_log($withdraw['member_id'],-$withdraw['money'],-$withdraw['money'],$content);
            // 如果余额小于100自动转账？先干掉，统统走财务人工
//            if($withdraw['money'] <= 100) {
//                $r = $aliPayService->withdrawal($withdraw['id'], $withdraw['money'], $withdraw['payment']);
//                if($r['code'] != 200) {
//                    $withdraw->status = 1;
//                    $withdraw->save();
//                    $account_flow::where('id', $account_id->id)->delete();
//                    return ['code'=>201, 'message'=>$r['msg']];
//                }
//            }
            return ['code'=>200, 'message'=>'success'];
        }else{
            return ['code'=>201, 'message'=>'请勿重复提交'];
        }
    }

    //充值手续费
    public function fee(Request $request)
    {
        $fee = AlipayLogs::where('status', '=', 1);
        $end = date('Y-m-d');
        //$fee->where('alipay_logs.status', '=', 1);
        if($request->get('phone')) {
            $fee->where('members.phone', 'like', '%'.$request->get('phone').'%');
        }
        $beginTime = $request->get('beginTime', '');
        $endTime = $request->get('endTime', '');
        if($beginTime) {
            $_endTime = $endTime ? $endTime : $end;
            $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
            $fee->whereBetween('alipay_logs.created_at', [$beginTime, $_endTime]);
        }
        $fee->leftJoin('members', 'alipay_logs.member_id', '=', 'members.id');
        $fee->orderBy('alipay_logs.id','desc');
        $c = $fee->select('alipay_logs.type','alipay_logs.id','alipay_logs.money','alipay_logs.poundage', 'alipay_logs.created_at', 'members.phone');
        $balance_sum = $c->sum('poundage');
        $_fee = $c->paginate();
        $page_sum = empty($_fee->toarray()) ? "" : array_sum(array_column($_fee->toarray()['data'],'poundage'));;
        return view('admin.finance.fee', compact('_fee', 'page_sum', 'balance_sum'));
    }

    //账户总计
    public function financeSum(){

//        $fee = AlipayLogs::where('status', '=', 1)->sum('real_money');
//        $withdraw = WithDraw::where('status','=',3)->sum('money');


        //支付宝余额

        // 支付宝充值总额
	    $ali_recharge = Finance::where('type','=','2')->where('asset_type', Account::BALANCE)->sum('balance');

	    // 后台大额充值总额
	    $admin_recharge = Finance::where('type','=','1')->where('asset_type', Account::BALANCE)->sum('balance');

	    // 支付宝提现总额（包含待审核）
	    $withdraw_amount = abs(Finance::where('type','=','4')->where('asset_type', Account::BALANCE)->sum('balance'));

	    // 用户购买艺奖堂小红花总花费

	    // 用户艺将堂缴纳保证金总额（不包含已退款）

        //交易手续费总额
        $trade_fee = abs(Finance::where('type','=','5')->sum('balance'));

        //用户持有现金数量
        $member_balance = Asset::where('asset_type','=',Account::BALANCE)->sum('amount');

        //会员持有藏品总数量
        $member_shares = Asset::where('asset_type','!=',Account::BALANCE)->sum('amount');

        // 总交易额
	    $trade_total = TradeLog::sum('total');

	    // 已提货藏品总数（包含待发货）
	    $delivery_amount = Delivery::where('stat', 'in', [0, 1])->sum('amount');

	    // 交易产生积分总额
	    $score_amount = ScoreLog::sum('score');

        return view('admin.finance.finance_sum',compact('withdraw_amount', 'ali_recharge',
	        'admin_recharge', 'trade_fee','member_balance','member_shares',
            'trade_total', 'delivery_amount', 'score_amount'
	    ));
    }
    //支付宝明细
    public function payMing(){
        $pay = AccountFlow::orderBy('id','desc')->paginate();
        return view('admin.finance.payMing', compact('pay'));
    }

    // 管理员添加提现
    public function withdrawCreate(Request $request)
    {
	    if ($request->isMethod('post')) {

	    	$pwd = $request->get('password');
		    if(openssl_private_decrypt(base64_decode($pwd), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
			    $pwd = $decrypted;
		    }

		    if(!Hash::check($pwd, Auth()->user()->password)){
				return ['code' => 202, 'data' => '管理员密码不正确'];
		    }

	    	$accountService = new AccountService();

		    $phone = $request->get('phone');

		    if(empty($phone)) {
		    	return ['code' => 201, 'data' => '手机号不得为空'];
		    }

		    $member = Member::where('phone', $phone)->first();

		    if(empty($member)) {
			    return ['code' => 201, 'data' => '用户不存在'];
		    }

		    $account = $member->account;

		    $money = intval($request->get('money'));
		    $payment = $request->get('payment');
		    $aliname = $request->get('aliname');

		    if(empty($payment) || empty($aliname)) {
			    return ['code' => 201, 'data' => '请输入提现支付宝信息'];
		    }

		    if (empty($money) || $money < 0) {
			    return ['code' => 201, 'data' => '提现数据有误'];
		    }

		    DB::beginTransaction();
		    try{
			    $balance = $accountService->balance($member->id);
			    if($balance < $money) {
				    throw new \Exception('可用现金不足');
			    }
			    $ret = WithDraw::create([
				    'member_id'=>$member->id,
				    'money'=>$money,
				    'payment'=>$payment,
				    'aliname'=>$aliname,
				    'status'=>1
			    ]);
			    if(!$ret) {
				    throw new \Exception('提现失败');
			    }
			    $accountService->addAsset($account->id, Account::BALANCE, '-' . $money, '');

			    $r = FinanceService::record($member->id, Account::BALANCE, Finance::WITHDRAW,'-'.$money,
				    0, '管理员提现到支付宝账号:'.$request->get('payment').',金额:'.$money.'元');
			    if(!$r) {
				    throw new \Exception('提现失败');
			    }
			    OpeLog::record('管理员提现到支付宝账号:'.$request->get('payment').',金额:'.$money.'元', [
				    'member_id'=>$member->id,
				    'money'=>$money,
				    'payment'=>$payment,
				    'aliname'=>$aliname,
				    'status'=>1
			    ], $phone);
			    DB::commit();
			    return ['code'=>200, 'data'=>'提现申请提交成功，请工作人员于2个工作日内内审核打款'];
		    } catch (\Exception $e) {
			    \Log::error($_REQUEST);
			    \Log::error($e->getTraceAsString());
			    DB::rollBack();
			    return ['code'=>202, 'data'=>$e->getMessage()];
		    }
	    } else {
		    $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
		    return view('admin.finance.withdraw_create', compact('key'));
	    }

    }


}