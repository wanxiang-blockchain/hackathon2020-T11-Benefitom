<?php
namespace App\Repository;


use App\Model\Account;
use App\Model\AssetType;
use App\Model\Member;
use App\Model\Finance;
use App\Model\ProjectOrder;
use App\Model\RechargeAudit;
use App\Model\User;
use App\Model\WithDraw;
class FinanceRepository{
    public function orders() {
        return ProjectOrder::all();
    }

    /**
     * 导出
     * @param $request
     * @return mixed
     */
    public function export($request,$recharge)
    {
        $data = Finance::leftjoin('finance_types','finances.type','=','finance_types.code')
            ->where(function($query)use($request,$recharge) {
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
                }else if($recharge == 2){
                    $query->whereIn('type',[1,2]);
                }
                if($beginTime) {
                    $_endTime = $endTime ? $endTime : $end;
                    $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                    $query->whereBetween('finances.created_at', [$beginTime, $_endTime]);
                }
            })->orderBy('finances.id','desc')
            ->select('finances.*','finance_types.name')
            ->get()
            ->map(function(&$order){
                unset($order['type']);
                unset($order['updated_at']);
                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                if($order['asset_type'] == Account::BALANCE){
                    $order['asset_type'] = '现金';
                }
                return $order;
            })->toArray();
        return $data;
    }

    public function withdrawExport($request){
        $data = WithDraw::leftjoin('members','with_draws.member_id','=','members.id')
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

            })->orderBy('with_draws.id','desc')
              ->select('with_draws.*','members.phone')
              ->get()
              ->map(function(&$order){
                    unset($order['updated_at']);
                    unset($order['phone']);
                    if($order['status'] == 1){
                        $order['status'] = '待审核';
                    }else if($order['status'] == 2){
                        $order['status'] = '已驳回';
                    }else if($order['status'] == 3){
                        $order['status'] ='已打款';
                    }
                  if($order['member_id']) {
                      $user = Member::where('id', $order['member_id'])->first();
                      $order['member_id'] = $user['phone'];
                  }
                    return $order;
              })->toArray();
           return $data;
    }
    /**
     * 导出
     * @param $request
     * @return mixed
     */
    public function exports($request)
    {
        $data = RechargeAudit::where(function($query)use($request) {
                $asset_type = $request->get('asset_type');
                $phone = $request->get('phone');
                $status = $request->get('status');
                $beginTime = request()->get('beginTime');
                $endTime = request()->get('endTime');
                $end = date('Y-m-d');
                if($asset_type){
                    $query->where('asset_type','=',$asset_type);
                }
                if($status){
                    $query->where('status',$status);
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
            })->orderBy('recharge_audits.id','desc')
            ->get()
            ->map(function(&$order){
                unset($order['type']);
                unset($order['updated_at']);
                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                if($order['audit_id'] != 0){
                    $users = User::where('id', $order['audit_id'])->first();
                    $order['audit_id'] = $users['name'];
                }else{
                    $order['audit_id'] = "无";
                    $order['audit_reason'] = "无";
                }
                if($order['status'] == 1){
                    $order['status'] = '审核中';
                }else if($order['status'] == 2){
                    $order['status'] ='已驳回';
                }else{
                    $order['status'] ='已通过';
                }
                if($order['asset_type'] == Account::BALANCE){
                    $order['asset_type'] = '现金';
                } else {
                	$aseetType = AssetType::where('code', $order['asset_type'])->first();
                    $order['asset_type'] = isset($aseetType['name']) ? $aseetType['name'] : '未知权益';
                }
                return $order;
            })->toArray();
        return $data;
    }

}