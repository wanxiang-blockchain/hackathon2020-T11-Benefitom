<?php
namespace App\Repository;


use App\Model\Member;
use App\Model\Project;
use App\Model\ProjectOrder;
class ProjectOrderRepository{
    public function orders() {
        return ProjectOrder::all();
    }

    public function create($data) {
       $_data = array_merge($data, ["order_id" => order_id("P")]);
       $project = Project::find($data['project_id']);
       $member_id = Member::where('phone', $data['phone'])->value('id');
       $_data['project_name'] = $project['name'];
       $_data['member_id']    = $member_id;
       return ProjectOrder::create($_data);
    }

    public function status($id, $status) {
        $order = ProjectOrder::find($id);
        $order->save();
        return $order;
    }

    /**
     * 导出
     * @param $request
     * @return mixed
     */
    public function export($request)
    {
        $projectOrder = new ProjectOrder();
        $data = $projectOrder->where(function($query)use($request){
            $order_id = $request->get('order_id');
            $project_id = $request->get('project_id');
            if($order_id) {
                $query->where('order_id', '=', $order_id);
            }
            if($project_id) {
                $query->where('project_id', '=', $project_id);
            }
        })->select('id','member_id', 'order_id', 'project_name','quantity', 'price', 'pay_type', 'created_at')
            ->get()
            ->map(function(&$order){
                if($order['pay_type'] == 1) {
                    $order['pay_type'] = '余额支付';
                } else if($order['pay_type'] == 2) {
                    $order['pay_type'] = '支付宝';
                }else if($order['pay_type'] == 3) {
                    $order['pay_type'] = '微信';
                }else if($order['pay_type'] == 4) {
                    $order['pay_type'] = '后台';
                }

                if($order['member_id']) {
                    $user = Member::where('id', $order['member_id'])->first();
                    $order['member_id'] = $user['phone'];
                }
                return $order;
            })->toArray();

        return $data;
    }

}