<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-07
 * Time: 15:57
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\BlockSale;
use App\Model\BlockTibi;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Btshop\BlockTiqu;
use App\Model\Member;
use App\Model\User;
use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BlockAssetController extends Controller
{
    public function index()
    {
        $query = BlockAssetLog::where(function ($query) {
            $phone = request()->get('phone');
            $type = request()->get('type');
            $code = request()->get('code');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($type){
                $query->where('type','=',$type);
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
            if ($code) {
                $query->where('code', $code);
            }
        });
        $totalSum = 0;
        if (request()->get('type')){
            $totalSum = $query->sum('amount');
        }
        $models = $query->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.blockasset.index', compact('models', 'totalSum'));
    }

    public function tiqu(Request $request)
    {
        $models = BlockTiqu::where(function ($query) use ($request){
            $type = $request->get('type');
            $phone = $request->get('phone');
            $stat = $request->get('stat');
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
            if($type){
                $query->where('type','=',$type);
            }
            if($stat){
                $query->where('stat','=',$stat);
            }

        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.blockasset.tiqu', compact('models'));
    }


    public function sale(Request $request)
    {
        $models = BlockSale::where(function ($query) use ($request){
            $stat = $request->get('stat');
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
            if($stat){
                $query->where('stat','=',$stat);
            }

        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.blockasset.sale', compact('models'));
    }

    public function saleAudit($id)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        \DB::beginTransaction();
        try{
            $model = BlockSale::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->stat !== BlockSale::STAT_INIT){
                throw new TradeException('该提取已处理');
            }
            $model->stat = BlockSale::STAT_DONE;
            $model->admin = $user->name;
            $model->save();
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function tiquAudit($id)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        \DB::beginTransaction();
        try{
            $model = BlockTiqu::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->stat !== BlockTiqu::STAT_INIT){
                throw new TradeException('该提取已处理');
            }
            $model->stat = BlockTiqu::STAT_DONE;
            $model->save();
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
           DB::rollBack();
           return ResUtil::error(201, $e->getMessage());
        }
    }

    public function tiquReject(\Illuminate\Http\Request $request)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $reason = $request->get('reason');
        $id = $request->get('id');
        if (empty($id) || empty($reason)) {
            return ResUtil::error(201, '参数不正确');
        }
        \DB::beginTransaction();
        try{
            $model = BlockTiqu::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->stat !== BlockTiqu::STAT_INIT){
                throw new TradeException('该提取已处理');
            }
            $model->stat = BlockTiqu::STAT_REJECT;
            $model->reason = $reason;
            $model->save();
            BlockAssetLog::record($model->member_id, $model->code, $model->amount, BlockAssetLog::TYPE_TI_BT_REJECT, '版通提取驳回 ' . $model->amount);
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }


    public function tibis(Request $request)
    {
        $models = BlockTibi::where(function ($query) use ($request){
            $stat = $request->get('stat');
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
            if($stat){
                $query->where('stat','=',$stat);
            }

        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.blockasset.tibi', compact('models'));
    }

    public function tibiAudit($id)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        \DB::beginTransaction();
        try{
            $model = BlockTibi::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->stat !== BlockTibi::STAT_INIT){
                throw new TradeException('该提取已处理');
            }
            $model->stat = BlockTibi::STAT_DONE;
            $model->auditor = $user->name;
            $model->save();
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function tibiReject(\Illuminate\Http\Request $request)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $reason = $request->get('reason');
        $id = $request->get('id');
        if (empty($id) || empty($reason)) {
            return ResUtil::error(201, '参数不正确');
        }
        \DB::beginTransaction();
        try{
            $model = BlockTibi::find($id);
            if (empty($model)){
                throw new TradeException(ApiResUtil::NO_DATA);
            }
            if ($model->stat !== BlockTibi::STAT_INIT){
                throw new TradeException('该提取已处理');
            }
            $model->stat = BlockTibi::STAT_REJECT;
            $model->note = $reason;
            $model->auditor = $user->name;
            $model->save();
            BlockAssetLog::record($model->member_id, BlockAssetType::CODE_ARTBC, $model->amount, BlockAssetLog::TYPE_TI_BI_REJECT, '版通提币驳回 ' . $model->amount);
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }
}