<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-22
 * Time: 14:26
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Btshop\BankDraw;
use App\Model\Finance;
use App\Model\Member;
use App\Model\User;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankcardController extends Controller
{


    public function index(Request $request)
    {
        $models = BankDraw::where(function ($query) use ($request){
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
            if($stat){
                $query->where('stat','=',$stat);
            }

        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.wallet.bankcard_draw', compact('models'));
    }

    public function audit($ids)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $ids = explode(',', $ids);
        try{
            DB::beginTransaction();
            foreach ($ids as $id){
                $model = BankDraw::find($id);
                if (empty($model)){
                    throw new TradeException(ApiResUtil::NO_DATA);
                }
                if ($model->stat !== BankDraw::STAT_INIT){
                    throw new TradeException('该提取已处理');
                }
                $model->stat = BankDraw::STAT_DONE;
                $model->auditor = $user->name;
                $model->save();
            }
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

    public function reject(Request $request, AccountService $accountService)
    {
        $user_id = Auth()->id();
        $user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }
        $reason = $request->get('reason');
        $ids = $request->get('ids');
        if (empty($ids) || empty($reason)) {
            return ResUtil::error(201, '参数不正确');
        }
        $ids = explode(',', $ids);
        try{
            DB::beginTransaction();
            foreach ($ids as $id) {
                $model = BankDraw::find($id);
                if (empty($model)){
                    throw new TradeException(ApiResUtil::NO_DATA);
                }
                if ($model->stat !== BankDraw::STAT_INIT){
                    throw new TradeException('该提取已处理');
                }
                $model->stat = BankDraw::STAT_REJECT;
                $model->note = $reason;
                $model->save();
                $accountId = $accountService->getAccountId($model->member_id);
                $accountService->addAsset($accountId , Account::BALANCE,  $model->amount, '');
                $r = FinanceService::record($model->member_id, Account::BALANCE, Finance::WALLET_BANKCARD_DARW_REJECT,
                    $model->amount, 0, '提现到银行卡驳回:' . $reason, $model->id);
                if(!$r) {
                    throw new \Exception('驳回失败');
                }
            }
            DB::commit();
            return ResUtil::ok();
        }catch (\Exception $e){
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }
    }

}