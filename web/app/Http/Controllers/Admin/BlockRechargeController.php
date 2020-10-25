<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-04
 * Time: 15:48
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockRechargeLog;
use App\Model\Member;
use App\Model\OpeLog;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use Illuminate\Support\Facades\Log;

class BlockRechargeController extends Controller
{
    public function index()
    {
        $models = BlockRechargeLog::where(function ($query) {
            $phone = request()->get('phone');
            $tx = request()->get('tx');
            $order_num = request()->get('order_num');
            if ($phone) {
                $mid = Member::fetchIdWithPhone($phone);
                if ($mid) {
                    $query->where('member_id', $mid);
                }
            }
            if ($tx){
                $query->where('tx', $tx);
            }
            if ($order_num){
                $query->where('order_num', $order_num);
            }
        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.blockrecharge.index', compact('models'));
    }


    public function del($id)
    {
        $user_id = Auth()->id();$user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }

        DB::beginTransaction();
        try {
            $model = BlockRechargeLog::find($id);
            if (empty($model)){
                throw new TradeException('no data');
            }
            if ($model->stat == 2){
//                $asset = BlockAsset::fetchModel($model->member_id, $model->code);
//                $asset->balance -= $model->amount;
//                $asset->save();
                BlockAssetLog::record($model->member_id, $model->code,
                    -1 * $model->amount, BlockAssetLog::TYPE_RECHARGE_DEL, '异常充值扣除');
            }
            $model->stat = BlockRechargeLog::STAT_FAILED;
            $model->save();
            DB::commit();
            return ['code' => 200, 'data' => '成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function revise($id)
    {
        $user_id = Auth()->id();$user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }

        DB::beginTransaction();
        try {
            $model = BlockRechargeLog::find($id);
            if (empty($model)){
                throw new TradeException('no data');
            }
            if ($model->stat == 2){
                throw new TradeException('nothing to revise');
            }
            $model->stat = BlockRechargeLog::STAT_DONE;
            BlockAssetLog::record($model->member_id, $model->code,
                $model->amount, BlockAssetLog::TYPE_RECHARGE_REVISE, '充值未到账修正');
            $model->save();
            OpeLog::record('block recharge revise', json_encode($model->toArray()), $model->id);
            DB::commit();
            return ['code' => 200, 'data' => '成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function txAppend(Request $request)
    {
        $user_id = Auth()->id();$user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $tx = $request->get('tx');
        $orderNum = $request->get('order_num');
        if (empty($tx) || empty($orderNum)) {
            return ['code'=>250,'data'=>'参数不正确'];
        }

        DB::beginTransaction();
        try {
            if (BlockRechargeLog::txExists($tx)){
                throw new TradeException('该交易已被绑定');
            }
            $model = BlockRechargeLog::fetchByOrderNum($orderNum);
            if (empty($model)){
                throw new TradeException('no data');
            }
            if (!empty($model->tx)){
                throw new TradeException('本记录已有tx');
            }
            $model->tx = $tx;
            $model->save();
            OpeLog::record('block recharge tx append', json_encode($model->toArray()), $model->id);
            DB::commit();
            return ['code' => 200, 'data' => '成功'];
        } catch (\Exception $e) {
            if (!$e instanceof TradeException){
                Log::error($e->getTraceAsString());
            }
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }
}