<?php

namespace App\Http\Controllers\Front;

use App\Helpers\SendSmsHelper;
use App\Model\AlipayLogs;
use App\Model\Artbc;
use App\Model\Asset;
use App\Model\AssetType;
use App\Model\Delivery;
use App\Model\TradeLog;
use App\Model\WithDraw;
use App\Service\FinanceService;
use App\Service\TradeService;
use App\Service\WechatPayService;
use App\Utils\ResUtil;
use Auth;
use App\Model\Member;
use App\Model\Account;
use App\Model\Finance;
use App\Model\TradeOrder;
use App\Service\SmsService;
use App\Model\ProjectOrder;
use Illuminate\Http\Request;
use App\Service\AccountService;
use App\Service\ValidatorService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{

	//用户管理中心首页
	public function index(AccountService $accountService, TradeOrder $tradeOrder, TradeLog $tradeLog)
	{
		$member_id = Auth::guard('front')->user()->id;
        $member = \DB::table('members')->where('id','=',$member_id)->first();
        $invite_num = \DB::table('members')->where('invite_member_id', '=', $member_id)->count();
        $account_id = $accountService->getAccountId($member_id);
		$data = [
			'total_amount' => $accountService->totalAmount($member_id),
			'balance' => $accountService->balance($member_id),
			'freeze_balance'=>$accountService->freeze_asset($member_id, Account::BALANCE),
			'freeze_share'=>$accountService->freeze_asset($member_id,Account::BALANCE, '!='),
			'has' => $accountService->notBalanceAmount($member_id),
			'project'=>
                \DB::table('project_orders')
                    ->where('member_id', '=',$member_id)
                    ->where('status', '>', 0)
                    ->sum('quantity'),
			'entrust' => $tradeOrder->where(['member_id'=>$member_id])->sum('quantity'),
			'trade'=>$tradeLog->WhereRaw('type = 1 and (buyer_id = '.$member_id.' or seller_id = '.$member_id.')')->sum('amount'),
            'invite_code'=>$member->invite_code,
            'invite_num'=>$invite_num,
			'score' => $accountService->fetchScore($account_id),
			'artbc' => Artbc::fetcyBalanceByMemberId($member_id)
		];

		return view('front.member.center.index',$data);
    }

	//我的认购
	public function subscription(Request $request)
	{
		$project = ProjectOrder::where(function($query)use($request) {
			$date = request()->get('date');
			$beginTime = request()->get('beginTime');
			$endTime = request()->get('endTime');
			$status = request()->get('status');
			$query->where('member_id', '=', $request->user('front')->id);
			if(in_array($status, [1,2,3,4])) {
				$status = $status == 4 ? 0 :$status;
				$query->where('status', '=', $status);
			}
			$end = date('Y-m-d');
			if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('created_at', [$beginTime, $_endTime]);
			}
		})->orderBy('id','desc')->paginate(10);
		$project->appends($request->all());
		return view('front.member.center.subscription', ['project'=>$project]);
	}

	//我的成交
	public function trade(Request $request)
	{
	    $trade = \DB::table('trade_logs');
        $date = request()->get('date', '');
        $beginTime = request()->get('beginTime');
        $endTime = request()->get('endTime');
        $status = request()->get('status');
        $trade->where('type', '=', 1);
        $member_id = Auth::guard('front')->id();
        if($status) {
            if($status == 1) {
                $trade->where('buyer_id', '=', $member_id);
            }elseif ($status == 2) {
                $trade->where('seller_id', '=', $member_id);
            }
        } else {
            $trade->WhereRaw("(buyer_id = ".$member_id .' or seller_id = '.$member_id.')');
        }
        $end = date('Y-m-d');
        if($beginTime) {
            $_endTime = $endTime ? $endTime : $end;
            $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
            $trade->whereBetween('trade_logs.created_at', [$beginTime, $_endTime]);
        }
		$trade = $trade->orderBy('trade_logs.id','desc')
            ->leftJoin('asset_types', 'trade_logs.asset_type', '=', 'asset_types.code')
            ->select('trade_logs.*', 'asset_types.name as asset_name')
            ->paginate(10);
		$trade->appends($request->all());
		return view('front.member.center.trade', compact('trade'));
	}

	//充值
	public function recharge(AccountService $accountService, WechatPayService $wechatPayService)
	{
		$member = Member::current();
        $member_id = $member->id;
	    if(request()->get('op') == 'ajax' ) {
	        if(request()->get('log_id')) {
                $log = AlipayLogs::where(['id'=>request()->get('log_id')])->orderby('id','desc')->first();
            } else {
                $log = AlipayLogs::where(['member_id'=>$member_id, 'status'=>0])->orderby('id','desc')->first();
            }
	        if($log && $log->status == 1) {
	            return ['code'=>200];
            }
            return ['code'=>201, 'data'=>$log];
        }
	    $is_weixin = is_weixin();
        $data = [
            'total_amount' => $accountService->totalAmount($member_id),
            'balance'      => $accountService->balance($member_id),
            'is_weixin' => $is_weixin,
	        'member' => $member,
        ];
		return view('front.member.center.new_recharge', $data);
	}

	public function withdraw(AccountService $accountService)
	{
		$member_id = Auth::guard('front')->user()->id;
		$key = trim(file_get_contents("../rsa_1024_pub.pem"));
		$data = [
			'total_amount' => $accountService->totalAmount($member_id),
			'balance' => $accountService->balance($member_id),
			'key' => $key
		];
		return view('front.member.center.withdraw',$data);
	}

	//委托管理
	public function entrust(Request $request)
	{
	    $trade = \DB::table('trade_orders');
        $beginTime = request()->get('beginTime');
        $endTime = request()->get('endTime');
        $status = request()->get('status');
        $type = request()->get('type');
        $trade->where('member_id', '=', $request->user('front')->id);
        if($status != null && in_array($status, [1,2,3,4])) {
            $status = $status == 4 ? 0 :$status;
            $trade->where('status', '=', $status);
        }
        if($type) {
            $trade->where('type', '=', $type);
        }
        $end = date('Y-m-d');
        if($beginTime) {
            $_endTime = $endTime ? $endTime : $end;
            $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
            $trade->whereBetween('trade_orders.created_at', [$beginTime, $_endTime]);
        }
        $trade = $trade->orderBy('id','desc')
            ->leftJoin('asset_types', 'trade_orders.asset_type', '=', 'asset_types.code')
            ->select('trade_orders.*', 'asset_types.name as asset_name')
            ->paginate(10);
		return view('front.member.center.entrust',compact("trade"));
	}

	//资金流水
	public function flow(Request $request)
	{
	    $flow = \DB::table('finances');
        $beginTime = request()->get('beginTime');
        $endTime = request()->get('endTime');
        $status = request()->get('status');
        $flow->where('member_id', '=', $request->user('front')->id);
        $flow->where('balance', '!=', 0);
        if($status == 1) {
            $flow->where('balance', '>', 0);
        } elseif($status ==2) {
            $flow->where('balance', '<', 0);
        }
        $end = date('Y-m-d');
        if($beginTime) {
            $_endTime = $endTime ? $endTime : $end;
            $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
            $flow->whereBetween('finances.created_at', [$beginTime, $_endTime]);
        }

		$flow = $flow->orderBy('finances.id','desc')
            ->leftJoin('finance_types', 'finances.type', '=', 'finance_types.code')
            ->leftJoin('asset_types', 'finances.asset_type', '=', 'asset_types.code')
            ->select('finances.*', 'finance_types.name as type_name', 'asset_types.name as asset_name')
            ->paginate(10);
		$flow->appends($request->all());
		return view('front.member.center.flow', compact('flow'));
	}

	//个人设置
	public function setting()
	{
        $id = Auth::guard('front')->user()->id;
        $member = Member::current();
        $addr = Artbc\Addr::fetchByMemberId($id);
        if (!$addr){
        	$addr = new Artbc\Addr();
        }
        $trade_pwd = Account::where(['member_id'=>$id])->pluck('trade_pwd')->first();
        return view('front.member.center.setting',compact('trade_pwd', 'member', 'addr'));
	}

	//更换手机号
	public function oneChangePhone()
	{
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
		return view('front.member.center.onechangephone', compact('key'));
	}

	//更换手机号步骤一
	public function editOneChangePhone(Request $request, SmsService $service, ValidatorService $validatorService)
	{
	    $data = $request->all();
        if (openssl_private_decrypt(base64_decode(trim($data['changePhoneOneCode'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['changePhoneOneCode'] = $decrypted;
        } else {
            die('');
        }
		$validatorService->checkValidator([
			'changePhoneOnePhone' => 'required|regex:/^1[34578]\d{9}$/',
		], $data);
		if($request->get('changePhoneOnePhone') != Auth::guard('front')->user()->phone) {
			return ['code' => 201, 'data' => '请填写当前登录用户手机'];
		}
		if($request->has('changePhoneOneCaptcha')) {
			if (!captcha_check($request->get('changePhoneOneCaptcha'))) {
				return ['code' => 201, 'data' => '请输入正确的图文验证码'];
			}
		}
		$validator = $service->verifyCode($data['changePhoneOneCode'], $request->get('changePhoneOnePhone'));
		if($validator['code'] != 200) {
			return $validator;
		}
        $code = session('verify_code');
        $code['valid'] = 0;
        session(['verify_code'=>$code]);
		return ['code' => 200, 'data' => '验证成功'];
	}

	//更换手机号步骤二
	public function twoChangePhone()
	{
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view('front.member.center.twochangephone',compact('key'));
	}

	//更换手机号步骤二
	public function editTwoChangePhone(Request $request, SmsService $service, ValidatorService $validatorService)
	{
        $data = $request->all();
        if (openssl_private_decrypt(base64_decode(trim($data['changePhoneTwoCode'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['changePhoneTwoCode'] = $decrypted;
        } else {
            die('');
        }

		if($request->get('changePhoneTwoPhone') == Auth::guard('front')->user()->phone) {
			return ['code' => 201, 'data' => '手机号没变化,无需更换'];
		}
		$validatorService->checkValidator([
			'changePhoneTwoPhone' => 'required|regex:/^1[34578]\d{9}$/|unique:members,phone',
		],$data);
		if($request->has('changePhoneTwoCaptcha')) {
			if (!captcha_check($request->get('changePhoneTwoCaptcha'))) {
				return ['code' => 201, 'data' => '请输入正确的图文验证码'];
			}
		}
		$validator = $service->verifyCode($data['changePhoneTwoCode'], $request->get('changePhoneTwoPhone'));
		if($validator['code'] != 200) {
			return $validator;
		}
		$newPhone = $request->get('changePhoneTwoPhone');
		if(Member::where('phone', $newPhone)->exists()) {
			return ['code' => 201, 'data' => '新手机号已被注册'];
		}

		$member = Member::find(Auth::guard('front')->user()->id);
		$member->phone =  $newPhone;
		$r = $member->save();
		if($r) {
            $code = session('verify_code');
            $code['valid'] = 0;
            session(['verify_code'=>$code]);
			return ['code' => 200, 'data' => '成功'];
		}
		return ['code' => 201, 'data' => '失败'];
	}

	//重置交易密码
	public function resetTradePassword()
	{
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
		return view('front.member.center.resettradepassword', ['key'=>$key]);
	}

    public function resetTradePasswordSuccess()
    {
        return view('front.member.center.resettradepasswordsuccess');
	}

	public function editResetTradePassword(Request $request, SmsService $service, ValidatorService $validatorService)
	{
		if($request->get('resetTradePwPhone') != Auth::guard('front')->user()->phone) {
			return ['code' => 201, 'data' => '手机号不是登录状态的手机号,请重新输入!'];
		}
		$data = $request->all();
		if(openssl_private_decrypt(base64_decode($data['resetTradePwNewPassword']), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
		    $data['resetTradePwNewPassword'] = $decrypted;
        } else {
		    die('');
        }
        if(openssl_private_decrypt(base64_decode($data['resetTradePwNewPassword_confirmation']), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))){
            $data['resetTradePwNewPassword_confirmation'] = $decrypted;
        } else {
            die('');
        }
        if(openssl_private_decrypt(base64_decode($data['resetTradePwCode']), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))){
            $data['resetTradePwCode'] = $decrypted;
        } else {
            die('');
        }
		$validatorService->checkValidator([
			'resetTradePwPhone' => 'required|regex:/^1[34578]\d{9}$/',
			'resetTradePwNewPassword'=>'required|min:6',
		], $data);
        $id = Auth::guard('front')->user()->id;
        $user = Member::where(['id'=>$id])->first();
        if($data['resetTradePwNewPassword_confirmation'] != $data['resetTradePwNewPassword']) {
            return ['code'=>201, 'data'=>'两次交易密码输入不一致'];
        }
        if(\Hash::check($data['resetTradePwNewPassword'], $user->password)) {
            return ['code'=>201, 'data'=>'交易密码不能于登录密码一致,请重新设置'];
        }
        $member = Account::where(['member_id'=>$id])->first();
		$validator = $service->verifyCode($data['resetTradePwCode'], $request->get('resetTradePwPhone'));
		if($validator['code'] != 200) {
		    return $validator;
		}

		$member->trade_pwd = Hash::make($data['resetTradePwNewPassword']);
		$member->save();
        $code = session('verify_code');
        $code['valid'] = 0;
        session(['verify_code'=>$code]);
		return ['code'=>200, 'data'=>'修改成功'];
	}

	//提现
    public function postWithDraw(AccountService $accountService, Request $request)
    {
	    return ['code'=>201, 'data'=>'绍德堂闭盘期间，提现功能暂停使用，恢复时间请关注绍德堂公告。'];
        $member_id = request()->user('front')->id;
        $money = intval($request->get('money'));
        $tradePassword = $request->get('tradePassword');
	    $account = Account::where(['member_id'=>$member_id])->first();
	    $payment = $request->get('payment');
	    $aliname = $request->get('aliname');

	    if(empty($payment) || empty($aliname)) {
	    	return ['code' => 201, 'data' => '请输入提现支付宝信息'];
	    }

	    if (empty($money) || $money < 0) {
		    return ['code' => 201, 'data' => '提现数据有误'];
	    }

	    if(empty($account ->trade_pwd)){
		    return ['code'=>220,'data'=>'请先设置交易密码, <a target="_blank" href="/member/resetTradePassword">去设置</a>'];
	    }

	    // 半小时内可使用交易remember_token
	    $decrypted = "";
	    if (openssl_private_decrypt(base64_decode($tradePassword),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
		    $tradePassword = $decrypted;
	    }

	    if(!(\Hash::check($tradePassword, $account ->trade_pwd))) {
		    return ['code'=>202, 'data'=>'交易密码不正确'];
	    }

        DB::beginTransaction();
        try{
	        $balance = $accountService->balance($member_id);
	        if($balance < $money) {
	        	throw new \Exception('可用现金不足');
	        }
	        if ($money < 2) {
		        throw new \Exception('提现金额不足手续费');
	        }
	        $ret = WithDraw::add($member_id, $money, $payment, $aliname);
	        if(!$ret) {
	        	throw new \Exception('提现失败');
	        }
	        $accountService->addAsset($account->id, Account::BALANCE, '-' . $money, '');

	        $r = FinanceService::record($member_id, Account::BALANCE, Finance::WITHDRAW,'-'.$money,
		        0, '提现到支付宝账号:'.$request->get('payment').',金额:'.$money.'元');
	        if(!$r) {
		        throw new \Exception('提现失败');
	        }
	        SendSmsHelper::withdrawNotice($account->member->phone);
	        DB::commit();
	        return ['code'=>200, 'data'=>'提现申请提交成功,工作人员将2个工作日内内审核打款'];
        } catch (\Exception $e) {
	        \Log::error($_REQUEST);
        	\Log::error($e->getTraceAsString());
        	DB::rollBack();
	        return ['code'=>202, 'data'=>$e->getMessage()];
        }
	}

	//我的资产
    public function asset(Request $request, AccountService $accountService)
    {
        $member_id = $request->user('front')->id;
        $asset = \DB::table('assets');
        $beginTime = request()->get('beginTime');
        $endTime = request()->get('endTime');
        $is_lock = request()->get('is_lock', '');
        $code = request()->get('code', '');
        $account_id = $accountService->getAccountId($member_id);
        $asset->where('account_id', '=', $account_id);
        $asset->where('deleted_at', '=', null);
        $asset->where('amount', '>', 0);
        if($is_lock == 1) {
            $asset->where('is_lock', '=', 1);
        } elseif($is_lock == 2) {
            $asset->where('is_lock', '=', 0);
        }
        if($code) {
            $asset->where('asset_type', '=', $code);
        }
        $end = date('Y-m-d');
        if($beginTime) {
            $_endTime = $endTime ? $endTime : $end;
            $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
            $asset->whereBetween('assets.unlock_time', [$beginTime, $_endTime]);
        }
        $assets = $asset->orderBy('assets.id','desc')
            ->leftJoin('asset_types', 'assets.asset_type', '=', 'asset_types.code')
            ->select('assets.*', 'asset_types.name as asset_name')
            ->paginate(10);
        $asset_types = AssetType::get();
        if($assets) {
            foreach ($assets as $asset) {
                $accountService->mergeAsset($member_id, $asset->asset_type);
                if ($asset->asset_type == Account::BALANCE || $asset->is_lock == 1) {
	                $asset->trade_amount = 0;
                }else {
	                $asset->trade_amount = $accountService->tPlusAssetAmount($member_id, $asset->asset_type);
                }
            }
        }
        return view('front.member.center.asset', compact('assets', 'asset_types', 'trade_amount'));
	}

	//K 线图
    public function Kchart(TradeService $tradeService)
    {
        $code = request()->get('code', '');
        if($code) {
            $asset_type = AssetType::where('code', '=', $code)->first();
        } else {
            return ['date'=>[], 'params'=>[]];
        }
        $type = request()->get('type', 1);
        return $tradeService->kChart($asset_type->code, $type);

	}

	//我的邀请记录
    public function invite()
    {

//    	if(!MemberService::isAgent()) {
//    		return redirect()->route('member/index');
//	    }

        $member_id = Auth::guard('front')->user()->id;
	    $where = ['invite_member_id' => $member_id];
	    $list = \DB::table('members')->where($where)->paginate();
	    $member = Member::where('id', $member_id)->select(['phone', 'invite_member_id', 'invite_code'])->first();

	    $dir = storage_path() . '/public/qrcode/';
	    if (!is_dir($dir)) {
		    mkdir($dir, 0755, true);
	    }
	    $file = $dir . $member['invite_code'] . '.png';
	    //            if (!file_exists($file)) {
	    file_put_contents($file, QrCode::format('png')->size(300)->margin(1)->errorCorrection('H')->mergeString(file_get_contents(public_path() . '/front/image/qrcode_logo.png'), .2)->generate(route('getRegister') . '?invite_member=' . $member['invite_code']));
	    //            }
	    $qrcode = '/storage/public/qrcode/' . $member['invite_code'] . '.png';

	    $invite_number = Member::where('invite_member_id', $member_id)->count();

	    // sub invite number
	    $sub_invite_number = DB::select('select count(1) as number from members where invite_member_id in (select id from members where invite_member_id = ? )', [$member_id]);

        return view('front.member.center.invite', compact('list', 'member', 'qrcode', 'invite_number', 'sub_invite_number'));
	}


	//我的邀请记录
	public function subinvite($phone)
	{

		$member = Member::current();
		$subMember = Member::fetchModelByPhone($phone);
		$list = [];
		$invite_number = 0;
		$err = '';
		if (empty($subMember) || ($subMember->invite_member_id!= $member->id)){
			$err = '该用户不存在或并非您邀请注册';
			return view('front.member.center.subinvite', compact('list', 'subMember', 'invite_number', 'err'));
		}

		$where = ['invite_member_id' => $subMember->id];
		$list = \DB::table('members')->where($where)->paginate();
		$invite_number = Member::where('invite_member_id', $subMember->id)->count();

		return view('front.member.center.subinvite', compact('list', 'subMember', 'invite_number', 'err'));
	}

	/**
	 * 提货申请
	 * @desc delivery
	 */
	public function delivery($id)
	{
		$member = Member::current();
		$asset = Asset::find($id);
		if(empty($asset) || $asset->account_id != $member->account->id) {
			return redirect('/member/deliveries');
		}
		$key = trim(file_get_contents("../rsa_1024_pub.pem"));
		return view('front.member.center.delivery', compact('key', 'asset'));
	}

	public function postDelivery(Request $request, ValidatorService $validatorService, AccountService $accountService)
	{

		$member_id = request()->user('front')->id;
		$member = Member::current();
		$tradePassword = $request->get('tradePassword');
		$account = Account::where(['member_id'=>$member_id])->first();
		$rule = [
			'amount' => 'required|numeric',
			'name' => 'required',
			'province' => 'required',
			'city' => 'required',
			'area' => 'required',
			'addr' => 'required',
			'phone' => 'required|numeric',
			'asset_id' => 'required'
		];
		$data = $request->all();

		$validator = $validatorService->checkValidator($rule, $data);

		if ($validator['code'] !== 200) {
			return $validator;
		}

		if(empty($account ->trade_pwd)){
			return ['code'=>220,'data'=>'请先设置交易密码, <a target="_blank" href="/member/resetTradePassword">去设置</a>'];
		}

		// 半小时内可使用交易remember_token
		$decrypted = "";
		if (openssl_private_decrypt(base64_decode($tradePassword),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
			$tradePassword = $decrypted;
		}

		if(!(\Hash::check($tradePassword, $account ->trade_pwd))) {
			return ['code'=>202, 'data'=>'交易密码不正确'];
		}

		try{

			DB::beginTransaction();
			$asset = Asset::find($data['asset_id']);

			if (!$asset || !$asset->unlock()) {
				return ['code'=>202, 'data'=>'数据有误'];
			}

			if( $asset->account_id != $member->account->id) {
				return ['code'=>202, 'data'=>'数据有误'];
			}

			// 判断数据够不够
			if ($asset->amount < $data['amount'] || $data['amount'] <= 0) {
				return ResUtil::error(201, '数量不够');
			}

			if ($data['amount'] % $asset->project->rule != 0) {
				return ResUtil::error(201, '请按' . $asset->project()->rule . '的倍数提货');
			}

			$data['member_id'] = $member_id;
			$data['asset_code'] = $asset->asset_type;
			// 记录提货信息
			if (!Delivery::create($data)) {
				throw new \Exception('服务器异常，请稍等再试');
			}

			// 扣除提货后资产数量
			$accountService->addAsset($account->id, $asset->asset_type, -1 * $data['amount']);

			DB::commit();
			SendSmsHelper::deliveryNotice($member->phone, $data['name']);
			return ResUtil::ok('提货成功，我们将在两个工作日内发货，请关注您的订货通览获取发货情况');

		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error($e->getTraceAsString());
			return ResUtil::error(201, $e->getMessage());
		}
	}

	public function deliveries(Request $request, AccountService $accountService)
	{
		$member_id = $request->user('front')->id;
		$member = Member::find($member_id);
		$asset = \DB::table('assets');
		$account_id = $accountService->getAccountId($member_id);
		$asset->where('account_id', '=', $account_id);
		$asset->where('deleted_at', '=', null);
		$asset->where('amount', '>', 0);
		$asset->where('is_lock', 0);
		$asset->where('asset_type', '!=', Account::BALANCE);

		$assets = $asset->orderBy('assets.id','desc')
			->leftJoin('asset_types', 'assets.asset_type', '=', 'asset_types.code')
			->select('assets.*', 'asset_types.name as asset_name')
			->paginate(10);
		$asset_types = AssetType::get();
		if($assets) {
			foreach ($assets as $asset) {
				$accountService->mergeAsset($member_id, $asset->asset_type);
				if ($asset->asset_type == Account::BALANCE || $asset->is_lock == 1) {
					$asset->trade_amount = 0;
				}else {
					$asset->trade_amount = $accountService->tPlusAssetAmount($member_id, $asset->asset_type, 0);
				}
			}
		}

		$deliveries = Delivery::where('member_id', $member_id)->paginate(10);

		$artbc = Artbc::fetcyBalanceByMemberId($member_id);

		return view('front.member.center.deliveries', compact('assets', 'asset_types', 'deliveries', 'artbc'));
	}

	public function userinfoEdit(Request $request)
	{
		$member = Member::current();
		if ($request->isMethod('GET')){
			return view('front.member.center.userinfoEdit', compact('member'));
		}else{
			$validatorService = new ValidatorService();
			$rule = [
				'name' => 'required',
				'idno' => 'required|string|between:18,18',
//				'sec_phone' => 'string',
				'sex' => 'required',
			];
			$data = $request->all();

			$validator = $validatorService->checkValidator($rule, $data);

			//
			$member->name = $data['name'];
			$member->idno = $data['idno'];
			$member->sec_phone = $data['sec_phone'];
			$member->sex = $data['sex'];
			if ($validator['code'] !== 200) {
				return view('front.member.center.userinfoEdit', compact('member', 'validator'));
			}

			$member->save();
			return redirect('member/setting');
		}
	}

	public function userinfo()
	{
		$member = Member::current();
		return view('front.member.center.userinfo', compact('member'));
	}


}
