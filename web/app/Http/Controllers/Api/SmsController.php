<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/8/15
 * Time: 下午4:34
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\Artbc\Msgcode;
use App\Model\Member;
use App\Service\SsoService;
use App\Utils\ApiResUtil;
use App\Utils\SmsUtil;
use Illuminate\Http\Request;

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

	public function verifySms(Request $request)
    {
        $member = Member::apiCurrent();
        $nationcode = $member->nationcode;
        $type = $request->get('usefor', 1);
        if (empty($nationcode)) {
            $msgcode = Msgcode::where('phone', $member->phone)->orderByDesc('id')->first();
            if (empty($msgcode)){
                return ApiResUtil::error('短信发送失败，请退出重新登录');
            }
            $nationcode = trim($msgcode->nationcode, '+');
        }
        $ret = SsoService::sms($member->phone, $nationcode, $type);
        if (!isset($ret['code'])) {
            return ApiResUtil::error('短信发送失败');
        }
        if ($ret['code'] == 0) {
            return ApiResUtil::ok();
        }
        return ApiResUtil::error($ret['data']);
    }
}