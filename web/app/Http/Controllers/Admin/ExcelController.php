<?php

namespace App\Http\Controllers\admin;
use App\Model\AlipayLogs;
use App\Model\Artbc\BtScoreLog;
use App\Model\Btshop\BankDraw;
use App\Model\Btshop\BlockAssetLog;
use App\Model\Btshop\BlockAssetType;
use App\Model\Btshop\BlockTiqu;
use App\Model\Btshop\BtshopDelivery;
use App\Model\Member;
use App\Model\User;
use App\Repository\AlipayLogsRepository;
use App\Repository\ProjectOrderRepository;
use App\Repository\FinanceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * 项目订单导出
     * @param Request $request
     * @param ProjectOrderRepository $projectOrderRepository
     */
    public function projectOrderExport(Request $request, ProjectOrderRepository $projectOrderRepository)
    {
        $data = $projectOrderRepository->export($request);
        $cellTitle = ['编号','用户','订单号','项目名称', '购买数量', '购买价格','支付方式', '下单时间'];
        array_unshift($data, $cellTitle);
        Excel::create('项目订单列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }
    /**
     * 财务充值到处
     * @param Request $request
     * @param FinanceRepository $financeRepository
     */
    public function financeExport(Request $request, FinanceRepository $financeRepository)
    {
        $data = $financeRepository->export($request,2);
        $cellTitle = ['编号','用户','充值金额', '充值数量', '资产类型','资产描述', '创建时间','充值类型'];
        array_unshift($data, $cellTitle);
        Excel::create('充值列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }
    /**
     * 财务日志导出
     * @param Request $request
     * @param FinanceRepository $financeRepository
     */
    public function logExport(Request $request, FinanceRepository $financeRepository)
    {
        $data = $financeRepository->export($request,1);
        $cellTitle = ['编号','用户','金额', '数量', '资产类型','资产描述', '创建时间','充值类型'];
        array_unshift($data, $cellTitle);
        Excel::create('充值列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }
    /**
     * 提现审核导出
     * @param Request $request
     * @param FinanceRepository $financeRepository
     */
    public function withdrawExport(Request $request, FinanceRepository $financeRepository)
    {
        $data = $financeRepository->withdrawExport($request);
        $cellTitle = ['编号','用户','金额', '支付宝账号', '驳回时间','驳回原因', '当前状态','申请时间'];
        array_unshift($data, $cellTitle);
        Excel::create('提现列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    //充值手续费
    public function feeExport(Request $request, AlipayLogsRepository $alipayLogsRepository)
    {
       $data = $alipayLogsRepository->export($request);
        $cellTitle = ['编号','用户','金额', '手续费','充值方式','时间'];
        array_unshift($data, $cellTitle);
        Excel::create('手续费详细', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    /**
     * 充值审核导出
     * @param Request $request
     * @param FinanceRepository $financeRepository
     */
    public function auditExport(Request $request, FinanceRepository $financeRepository)
    {
        $data = $financeRepository->exports($request);
        $cellTitle = ['编号','用户','单价', '充值数量', '资产类型','资产描述', '解锁时间','当前状态','操作人','驳回原因','添加时间'];
        array_unshift($data, $cellTitle);
        Excel::create('充值审核列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    /**
     * btscore_log export
     */
    public function btscoreExport(Request $request)
    {
        $data = BtScoreLog::where(function($query)use($request) {
            $type = $request->get('type', null);
//            $stat = $request->get('stat', null);
            $stat = BtScoreLog::STAT_INIT;
            $phone = $request->get('phone');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if(!is_null($type)){
                $query->where('type','=',$type);
            }
            if (!is_null($stat)){
                $query->where('stat',$stat);
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
        })->orderBy('bt_score_logs.id','desc')
            ->get()
            ->map(function(&$order){
                unset($order['type']);
                unset($order['updated_at']);
                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                $order['statLable'] = BtScoreLog::fetchStatLabel($order['stat']);
                $order['typeLabel'] = BtScoreLog::fetchTypeLabel($order['type']);
                return [
                    $order['id'],
                    $order['member_id'],
                    abs($order['amount']),
                    round(abs($order['amount']) * 0.8, 2),
                    $order['name'],
                    $order['card'],
                    $order['bank'],
                    $order['created_at']
                ];
            })->toArray();
        // ID	手机	数量	实际拨款	手续费	购物积分	变更类型	余额	版通账户	收款账户	收款账户名	收款开户行	创建时间	操作
        $cellTitle = ['编号', '手机', '提取数量', '实际拨款',  '姓名', '银行卡号', '开户行', '创建时间'];
        array_unshift($data, $cellTitle);
        Excel::create('版通提取列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    public function blockTiquExport(Request $request)
    {
        $data = BlockTiqu::where(function($query)use($request) {
            $type = $request->get('type', null);
            $stat = $request->get('stat', null);
            $phone = $request->get('phone');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if(!is_null($type)){
                $query->where('type','=',$type);
            }
            if (!is_null($stat)){
                $query->where('stat',$stat);
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
        })->orderBy('id','desc')
            ->get()
            ->map(function(&$order){
//                unset($order['type']);
//                unset($order['updated_at']);
                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                $order['statLable'] = BlockTiqu::statLabel($order['stat']);
                $order['typeLabel'] = BlockTiqu::typeLabel($order['type']);
                return [
                    $order['id'],
                    $order['member_id'],
                    BlockAssetLog::codeToName($order['code']),
                    $order['typeLabel'],
                    $order['statLable'],
                    $order['btaccount'],
                    $order['card'],
                    $order['name'],
                    $order['bank'],
                    abs($order['amount']),
                    $order['price'],
                    round($order['amount'] * $order['price'], 2),
                    $order['created_at']
                ];
            })->toArray();

        $cellTitle = ['编号', '手机', 'code', '提取类型', '提取状态', '版通账户', '收款账户', '收款人姓名', '开户行', '提取数量', '提取时价格', '打款金额', '创建时间'];
        array_unshift($data, $cellTitle);
        Excel::create('提取列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    public function btshopDeliveryExport(Request $request)
    {
        $data = BtshopDelivery::where(function($query)use($request) {
            $stat = $request->get('stat', null);
            $phone = $request->get('phone');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if (!empty($stat)){
                $query->where('stat',$stat);
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
        })->orderBy('id','desc')
            ->get()
            ->map(function(&$order){
                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                $order['statLable'] = BtshopDelivery::statLabel($order['stat']);
                return [
                    $order['id'],
                    $order['member_id'],
                    $order->product->name,
                    $order->order->amount,
                    $order['receiver'],
                    $order['receive_phone'],
                    $order['receive_province'],
                    $order['receive_city'],
                    $order['receive_area'],
                    $order['receive_addr'],
                    $order['created_at'],
                    $order['note'],
                    $order['statLable'],
                ];
            })->toArray();

        $cellTitle = ['编号', '手机', '产品', '数量', '收件人', '收件人手机号', '省', '币', '区', '地址', '提货时间', '备注', '状态'];
        array_unshift($data, $cellTitle);
        Excel::create('兑换中心-提货列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }

    public function bankcardDrawExport(Request $request)
    {
        $data = BankDraw::where(function($query)use($request) {
            $stat = $request->get('stat', null);
            $phone = $request->get('phone');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if (!empty($stat)){
                $query->where('stat',$stat);
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
        })->orderBy('id','desc')
            ->get()
            ->map(function(&$order){
                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                $order['statLable'] = BankDraw::statLabel($order['stat']);
                return [
                    $order['id'],
                    $order['member_id'],
                    $order['statLable'],
                    $order['card'] . ',',
                    $order['name'],
                    $order['headbank'],
                    $order['bank'],
                    $order['amount'],
                    $order['created_at'],
                ];
            })->toArray();

        $cellTitle = ['编号', '手机', '状态', '收款账户', '收款人姓名', '总行', '开户行', '数量', '创建时间'];
        array_unshift($data, $cellTitle);
        Excel::create('银行卡提现列表', function($excel)use($data){
            $excel->sheet('score', function($sheet) use ($data){
                $sheet->rows($data);
            });
        })->export('xls');
    }
}
