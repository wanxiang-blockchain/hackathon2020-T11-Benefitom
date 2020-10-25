<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-02-27
 * Time: 16:07
 */

namespace App\Http\Controllers\Openapi;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Member;
use App\Service\SsoService;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use App\Utils\DissysPush;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        $phone = $request->get('phone');
        $nationcode = $request->get('nationcode', '+86');
        if (!$phone) {
            return ApiResUtil::error(ApiResUtil::WRONG_PARAMS);
        }
        $ret = SsoService::sms($phone, $nationcode);
        if (!isset($ret['code'])) {
            return ApiResUtil::error('短信发送失败');
        }
        if ($ret['code'] == 0) {
            return ApiResUtil::ok();
        }
        return ApiResUtil::error($ret['data']);
    }

    public function verify(Request $request, ValidatorService $validatorService)
    {
        $data = $request->all();

        $validator = $validatorService->checkValidator(
            [
                'phone' => 'required',
                'verifycode' => 'required',
                'nationcode' => 'required',
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
                return ApiResUtil::ok();
            }
            // 如果未注册手机号
            $member = new Member();
            $data['wallet_invite_member_id'] = 0;
            $data['spid'] = 0;
            $data['invite_code'] = randStr();
            $data['uid'] = randStr(32);

            $ret = $member->create([
                'nationcode' => $data['nationcode'],
                'phone' => $data['phone'],
                'invite_code' => $data['invite_code'],
                'wallet_invite_member_id' => $data['wallet_invite_member_id'],
                'spid' => $data['spid'],
                'uid' => $data['uid']
            ]);
            $account_ret = Account::create(['member_id' => $ret->id, 'is_lock' => 0]);

            if (!$ret) {
                return ApiResUtil::error('注册失败');
            }
//            if (!DissysPush::reg($ret->id, '980000089')){
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
}