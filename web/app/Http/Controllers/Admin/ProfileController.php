<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-02-13
 * Time: 14:37
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Member;
use App\Model\Profile;
use App\Model\ProfileLog;
use App\Model\User;
use App\Utils\ApiResUtil;
use App\Utils\QcloudSms;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $models = ProfileLog::where(function ($query) {
            $phone = request()->get('phone');
            $idno = request()->get('idno');
            $verified = request()->get('verified');
            if ($phone) {
                $mid = Member::fetchIdWithPhone($phone);
                if ($mid) {
                    $query->where('member_id', $mid);
                }
            }
            if ($idno){
                $query->where('idno', $idno);
            }
            if (!is_null($verified)){
                $query->where('verified', $verified);
            }
        })->orderBy('created_at', 'desc')->paginate(10);
        foreach ($models as $model){
            Profile::fetchOssSign($model);
        }
        $models->appends(Request()->all());
        return view('admin.member.profiles', compact('models'));
    }


    public function audit($id)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 5, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        try{
            DB::beginTransaction();
            $model = ProfileLog::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->verified !== ProfileLog::VERIFIED_INIT){
                throw new TradeException('该实名已处理');
            }
            $model->verified = ProfileLog::VERIFIED_DONE;
            $model->auditor = $user->name;
            $model->save();
            $profile = new Profile();
            $profile->fill($model->toArray());
            $profile->save();
            DB::commit();
            QcloudSms::profileVerifyNotice($model->member->nationcode, $model->member->phone);
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function reject(Request $request)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 5, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $note = $request->get('note');
        $id = $request->get('id');
        if (empty($id) || empty($note)) {
            return ResUtil::error(201, '参数不正确');
        }
        try{
            DB::beginTransaction();
            $model = ProfileLog::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->verified !== ProfileLog::VERIFIED_INIT){
                throw new TradeException('该数据已处理');
            }
            $model->verified = ProfileLog::VERIFIED_REJECT;
            $model->note = $note;
            $model->save();
            DB::commit();
            QcloudSms::profileRejectNotice($model->member->nationcode, $model->member->phone);
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function revert(Request $request)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 5, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $note = $request->get('note');
        $id = $request->get('id');
        if (empty($id) || empty($note)) {
            return ResUtil::error(201, '参数不正确');
        }
        try{
            DB::beginTransaction();
            $model = ProfileLog::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }

            $model->verified = ProfileLog::VERIFIED_REJECT;
            $model->note = $model->note . ' ' . $note;
            $model->save();
            $profile = Profile::fetchByMid($model->member_id);
            $profile->delete();
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

}