<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/5/7
 * Time: 上午10:33
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Artbc\WalletInvite;
use App\Model\BlockTransferLog;
use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Btshop\SuperMember;
use App\Model\Member;
use App\Model\Passport\User;
use App\Service\AccountService;
use App\Service\MemberService;
use App\Service\SmsService;
use App\Service\SsoService;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\DissysPush;
use App\Utils\DisVerify;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use spec\Prophecy\Promise\RequiredArgumentException;

class MemberController extends Controller
{

	protected $validatorService;
	protected $request;

	public function __construct(ValidatorService $_validator)
	{
		$this->validatorService = $_validator;
	}

	public function profile(Request $request)
	{
		$phone = $request->post('phone');
		$member = Member::fetchModelByPhone($phone);
		if (!$member) {
			return ResUtil::error(201, '用户不存在');
		}

		return ResUtil::ok($member);
	}

	public function balance(Request $request, AccountService $accountService)
    {
        $member = Member::apiCurrent();
        $balance = $accountService->balance($member->id);
        $ArTBCBalance = BlockAsset::codeBalance($member->id, BlockAssetType::CODE_ARTBC);
        $ARTTBCBalance = BlockAsset::codeBalance($member->id, BlockAssetType::CODE_ARTTBC);

        $ARTBCSBalance = BlockAsset::codeBalance($member->id, BlockAssetType::CODE_ARTBCS);
        $ARTBCSCanCash = BlockAsset::artbcsCanCash($member->id);
        $ARTBCSCanCash > $ARTBCSBalance && $ARTBCSCanCash = $ARTBCSBalance;

        return ApiResUtil::ok(compact('balance', 'ArTBCBalance', 'ARTTBCBalance', 'ARTBCSCanCash', 'ARTBCSBalance'));
    }

	public function myProfile(Request $request)
    {
        $member = Member::apiCurrent();
        return ResUtil::ok($member);
    }

	public function reg(Request $request)
	{
//	    return ApiResUtil::error('系统升级，暂停注册服务');
		$data = $request->all();

		$validator = $this->validatorService->checkValidator(
		    [
		        'phone' => 'required',
                'verifycode' => 'required',
                'nationcode' => 'required',
                'invite_code' => 'required|string'
            ],
            $data);
		if ($validator['code'] != 200) {
			return ApiResUtil::error($validator['data']);
		}
        $verify = SsoService::smsVerify($data['phone'], $data['nationcode'], $data['verifycode']);
        if ($verify['code'] !== 0){
            return ApiResUtil::error($verify['data']);
        }
        DB::beginTransaction();
        try{
            $member = Member::where(['phone' => $request->input('phone')])->first();
            if ($member) {
                if ($member->is_lock) {
                    return ApiResUtil::error(ApiResUtil::FUCKED_MAN);
                }

                return ApiResUtil::error('该账号已注册过，请直接前往登录。');
            }
            $member = new Member();
            $parent = Member::fetchModelWithInviteCode($data['invite_code']);
            if (!$parent) {
                throw new TradeException('邀请人不存在');
            }
            $data['wallet_invite_member_id'] = $parent->id;
            $data['spid'] = $parent->spid > 0 ? $parent->spid : $parent->id;
            $data['invite_code'] = randStr();
            $data['uid'] = randStr(32);

            \Log::debug($data);
            $ret = $member->create([
                'nationcode' => $data['nationcode'],
                'phone' => $data['phone'],
                'invite_code' => $data['invite_code'],
                'wallet_invite_member_id' => $data['wallet_invite_member_id'],
                'spid' => $data['spid'],
                'uid' => $data['uid']
            ]);
            $account_ret = Account::create(['member_id' => $ret->id, 'is_lock' => 0]);
            $parent2 = $parent;
            if ($parent2) {
                $level = 1;
                do {
                    WalletInvite::add($ret->id, $parent2->id, $level);
                    if ($parent2->wallet_invite_member_id > 0) {
                        $level ++;
                        $parent2 = Member::find($parent2->wallet_invite_member_id);
                    }else{
                        $parent2 = null;
                    }
                }while($parent2);
            }

            if (!$ret) {
                return ApiResUtil::error('注册失败');
            }
//            if (!DissysPush::reg($ret->id, $parent->phone)){
//                throw new TradeException('推送积分系统失败');
//            }
            DB::commit();
            return ApiResUtil::ok();
        }catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e->getTraceAsString());
            return ApiResUtil::error($e->getMessage());
        }
	}

	//登录
	public function login(Request $request)
	{
		$data = $request->all();

		$validator = $this->validatorService->checkValidator(
		    [
		        'phone' => 'required',
                'nationcode' => 'required',
                'verifycode' => 'required'
            ], $data);
		if ($validator['code'] != 200) {
			return ApiResUtil::error($validator['data']);
		}
		$member = Member::where(['phone' => $request->input('phone')])->first();
		if ($member) {
			if ($member->is_lock) {
				return ApiResUtil::error(ApiResUtil::FUCKED_MAN);
			}
			$verify = SsoService::smsVerify($data['phone'], $data['nationcode'], $data['verifycode']);
			if ($verify['code'] !== 0){
			    return ApiResUtil::error($verify['data']);
            }
            $tk = randStr(60);
            $phone = $data['phone'];;
            $invite_code = $member->invite_code;
            $member->tk = $tk;
            if (empty($member->nationcode)){
                $member->nationcode = $data['nationcode'];
            }
            if ($member->save()){
                $is_super = intval(SuperMember::isSuper($member->phone));
                return ApiResUtil::ok(compact('tk', 'phone', 'invite_code', 'is_super'));
            }
            return ApiResUtil::error('服务器异常');
		}

		return ApiResUtil::error(ApiResUtil::LOGIN_FAIL);
	}


	public function resetPwd(Request $request, Member $member, SmsService $service)
	{
	    return 404;
		$data = $request->all();

		$validator = $this->validatorService->checkValidator([
			'phone' => 'required',
			'verifycode' => 'required',
			'password' => 'required'
		], $data);
		if ($validator['code'] != 200) {
			return ApiResUtil::error($validator['data']);
		}
		$member = Member::fetchModelByPhone($data['phone']);
		if (!$member) {
		    // 如果trade库里查不到，查passport库
            $user = User::where('phone', $data['phone'])->first();
            if (!$user){
    			return ApiResUtil::error('该账号不存在，请前往注册！');
            }
            $user  = [
                'phone' => $data['phone'],
                'invite_code' => randStr(),
                'uid' => $user['uid']
            ];
            $member = new Member();
            try{
                $ret = $member->create($user);
            } catch ( Exception $e) {
                throw new \ErrorException('数据写入失败');
            }
            if (empty($ret)) {
                throw new \ErrorException('数据写入失败');
            }
            $account_ret = Account::create(['member_id'=>$ret->id, 'is_lock'=>0]);
		}else{
            if ($member->is_lock) {
                return ApiResUtil::error(ApiResUtil::FUCKED_MAN);
            }
        }

		// 对接sso 重置密码
		$ret = SsoService::resetPwd($data['phone'], $data['password'], $data['verifycode']);

		if ($ret['code'] != 0) {
			return ApiResUtil::error($ret['data']);
		}

		return ApiResUtil::ok('修改成功，请前往登录');

	}

	public function myWalletInvite(Request $request)
    {
        $member = Member::apiCurrent();
        $lastId = $request->get('lastId', 0);

        $query = Member::where('wallet_invite_member_id', $member->id);
        if ($lastId > 0){
            $query->where('id', '<', $lastId);
        }
        $count = $query->count('id');
        $list = $query->orderByDesc('id')->select(['id', 'phone', 'created_at'])->limit(200)->get();
        \Log::debug($member->toArray());
        $parent = Member::walletParent($member->wallet_invite_member_id);
        $parentPhone = $parent ? $parent->phone : '';
        return ApiResUtil::ok([
            'list' => $list,
            'hasMore' => count($list) == 200,
            'parentPhone' => $parentPhone,
            'count' => $count
        ]);
    }


    //我的邀请记录
    public function subinvite(Request $request)
    {

        $member = Member::apiCurrent();
        $lastId = $request->get('lastId', 0);
        // 如果有phone,就是取二级
        $phone = $request->get('phone');
        $subMember = Member::fetchModelByPhone($phone);
        \Log::info($subMember);
        if (empty($subMember) || ($subMember->wallet_invite_member_id != $member->id)){
            return ApiResUtil::error('该用户不存在或并非您邀请注册');
        }
        $query = Member::where('wallet_invite_member_id', $subMember->id);
        if ($lastId > 0){
            $query->where('id', '<', $lastId);
        }
        $count = $query->count('id');
        $list = $query->orderByDesc('id')->select(['id', 'phone', 'created_at'])->limit(20)->get();
        return ApiResUtil::ok([
            'list' => $list,
            'hasMore' => count($list) == 20,
            'count' => $count
        ]);
    }

    public function appendInviteCode(Request $request)
    {
//        return ApiResUtil::error('系统升级，暂停服务');
        $member = Member::apiCurrent();
        $code = $request->get('code', '');
        if (empty($code)) {
            return ApiResUtil::error('邀请码不得为空');
        }
        \DB::beginTransaction();
        try{
            $defaultInvite = Member::where('phone', '18611331597')->first();
            if ($member->wallet_invite_member_id > 0 && $member->wallet_invite_member_id !== $defaultInvite->id) {
                return ApiResUtil::error('您已有邀请人');
            }
            $parent = Member::fetchModelWithInviteCode($code);
            // 自己不能邀请自己
            if (!$parent) {
                throw new TradeException('邀请码不正确');
            }
            if ($parent->id === $member->id) {
                throw new TradeException('自己不能邀请自己');
            }
            if ($parent->id > $member->id) {
                throw new TradeException('后注册人不可邀请先注册人');
            }
            $member->wallet_invite_member_id = $parent->id;
            $member->spid = $parent->spid > 0 ? $parent->spid : $parent->id;
            if($member->id === $member->spid){
                throw new TradeException('邀请关系产生循环，不可成立。');
            }
            if (!$member->save()){
                throw new TradeException('数据写入失败');
            }
            // 修改所有该成员下级超父为
            Member::where('spid', $member->id)->update(['spid' => $member->spid]);
            $parent = Member::find($member->wallet_invite_member_id);
            $level = 1;
            $parent2 = $parent;
            while($parent2) {
                if ($member->id == $parent2->id){
                    throw new TradeException('邀请关系产生循环，不可成立。');
                }
                WalletInvite::add($member->id, $parent2->id, $level);
                if ($parent2->wallet_invite_member_id > 0) {
                    $parent2 = Member::find($parent2->wallet_invite_member_id);
                    $level++;
                } else {
                    $parent2 = null;
                }
            }
            // 清洗该成员所有下级
            RedisUtil::lpush(RedisKeys::WALLET_INVITE_FLUSH_LIST, $member->id);

            // 推送积分系统
//            if (!DissysPush::appendParent($member->phone, $parent->phone)){
//                throw new TradeException('推送积分系统失败');
//            }
            \DB::commit();
            return ApiResUtil::ok(['parentPhone' => $parent->phone]);
        }catch (\Exception $e) {
            \DB::rollBack();
            if (!$e instanceof TradeException){
                Log::debug($e->getTraceAsString());
            }
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function tmpTk()
    {
//        return ApiResUtil::error('系统升级，暂停服务');
        $member = Member::apiCurrent();
        $ticket = DisVerify::makeTk($member->id);
        return ApiResUtil::ok(compact('ticket'));
    }
}