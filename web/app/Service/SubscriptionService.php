<?php
namespace App\Service;

use App\Model\Asset;
use App\Model\ProjectOrder;
use App\Model\TradeLog;
use App\Model\Project;
use DB;
use League\Flysystem\Exception;

class SubscriptionService {
    /**
     * @var ProjectOrder
     */
    private $order;
    /**
     * @var AccountService
     */
    private $accountService;
    /**
     * @var FinanceService
     */
    private $financeService;

    /**
     * SubscriptionService constructor.
     * @param ProjectOrder $order
     * @param AccountService $accountService
     */
    function __construct(ProjectOrder $order, AccountService $accountService, FinanceService $financeService) {
        $this->order = $order;
        $this->accountService = $accountService;
        $this->financeService = $financeService;
    }

    /**
    *   建立一个订单
    *   $project_id 为项目id
    *   $user_id    订单所有人
    *   $quantity   交易数量
    *   $price      交易价格
    */
    function makeOrder($project_id, $member_id, $quantity, $price)
    {
        return ProjectOrder::create([
            'project_id' => $project_id,
            'project_name' => Project::find($project_id)->name,
            'order_id' => order_id(),
            'status' => 0,
            'member_id' => $member_id,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }


    /**
    *   取消订单(如果是已经交易状态，不能撤销)
    *   $user_id   用户id
    *   $order_id  订单id
    */
    function cancelOrder($order_id) {
        $order = Project::find($order_id);
        if ($order) {
            $order->status = 3;
            $order->save();
            return true;
        } else {
            return false;
        }
    }


    /*
    *   支付订单，根据余额和剩余股份进行支付
    *    @param $order_id  订单id
    *    @return boolean   成功返回true, 失败返回false
    */
    function payOrder($order_id) {
        \DB::beginTransaction();
        $order      = ProjectOrder::findOrFail($order_id);
        $member_id  = $order->member_id;
        $project_id = $order->project_id;
        $project    = Project::lockForUpdate()->findOrFail($project_id);
        $account_id = $this->accountService->getAccountId($member_id);
	    $perOrder = DB::select('select sum(quantity) as sum from project_orders where member_id = :mid and project_id = :pid and status  = 2;',
		    ['mid' => $member_id, 'pid' => $project_id]);
	    $sum = isset($perOrder[0]->sum) ? $perOrder[0]->sum : 0;
	    if ($project->per_limit < $sum + $order->quantity) {
		    \DB::rollback();
		    throw new Exception('超过单人限购额度');
	    }
        if ($this->accountService->balance($member_id) < $order->price * $order->quantity) {
            \DB::rollback();
            throw new Exception('余额不足');
        }
        if ($project->limit < $project->sold() + $order->quantity) {
            \DB::rollback();
            throw new Exception('库存不足');
        }

        $order->status = 2;
        $order->pay_type = 1;
        $order->save();
        $project->position += $order->quantity;
        $project->save();
        $this->accountService->recharge($account_id, -1 * $order->price * $order->quantity);
        // 取出原持有数
	    $asset = Asset::fetchAssetData($account_id, $project->asset_code);
        $this->accountService->addAsset($account_id, $project->asset_code, $order->quantity);
	    // 计算成本价
	    $this->accountService->buyCost($asset['cost'], $asset['amount'], $order->price, $order->quantity, $account_id, $project->asset_code);
        $this->financeService->subscription($member_id,
            $project->asset_code,
            -1 * $order->price * $order->quantity,
            $order->quantity,
            "认购|{$project->name}|{$order->quantity}份");
        // 认购之后，记录余额明细
        \DB::commit();
    }
}