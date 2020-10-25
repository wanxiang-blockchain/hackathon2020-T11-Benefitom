<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-02-13
 * Time: 11:25
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Profile;
use App\Model\ProfileLog;
use App\Model\Member;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function userinfo()
    {
        $member = Member::apiCurrent();
        $profile = Profile::fetchByMid($member->id);
        if (!$profile) {
            $profile = ProfileLog::fetchLastByMid($member->id);
            if($profile){
                unset($profile->auditor);
            }
        }
        $data = [];
        if ($profile) {
            $data = Profile::fetchOssSign($profile);
        }
        return ApiResUtil::ok($data);

    }

    public function verify(Request $request, ValidatorService $validatorService)
    {
        //appid  | string | 1 | appid
        //name | string | 1 | name
        //idno | string | 1 | id number
        //sex  | int  | 1  | 0 女 1 男
        //id_img | string  |  1 | 身份证正面照
        //id_back_img | string | 1 | 身份证背面照
        //id_hold_img | string  | 1  | 手持身份证照片
        $data = $request->all();
        $validator = $validatorService->checkValidator([
            'name' => 'string|required',
            'idno' => 'string|required',
            'sex' => 'required|numeric|in:1,0',
            'id_img' => 'string|required',
            'id_back_img' => 'string|required',
            'id_hold_img' => 'string|required',
        ], $data);
        if ($validator['code'] !== 200){
            return ApiResUtil::error($validator['data']);
        }
        $member = Member::apiCurrent();
        DB::beginTransaction();
        try{
            // 是否已实名
            if (Profile::isMemberVerified($member->id)){
                throw new TradeException('您已实名');
            }
            // 是否有审的
            if (ProfileLog::fetchByMidVerified($member->id, ProfileLog::VERIFIED_INIT)){
                throw new TradeException('您尚有实名待审核');
            }
            // 如果是上线之中后注册的，只能实名一外
//            $date = '2019-03-12 00:00:00';
//            if ($member->created_at > $date){
//                if (ProfileLog::idnoUsed($data['idno'])){
//                    throw new TradeException('该身份证已被实名');
//                }
//            }
            ProfileLog::create([
                'member_id' => $member->id,
                'name' => $data['name'],
                'idno' => $data['idno'],
                'sex' => $data['sex'],
                'id_img' => $data['id_img'],
                'id_back_img' => $data['id_back_img'],
                'id_hold_img' => $data['id_hold_img'],
            ]);
            DB::commit();
            return ApiResUtil::ok();
        }catch (TradeException $e){
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        }catch (\Exception $e){
            \Log::error($e->getTraceAsString());
            DB::rollBack();
            return ApiResUtil::error('数据提交失败');
        }
    }
}