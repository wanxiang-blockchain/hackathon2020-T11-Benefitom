<?php

namespace App\Http\Controllers\Admin;

use App\Model\Project;
use App\Model\ProjectOrder;
use App\Repository\ProjectOrderRepository;
use App\Service\ProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProjectOrderController extends Controller
{
    //
    public function projectOrder(Request $request)
    {
        $projectOrder = new ProjectOrder();
        $orders = $projectOrder->leftjoin('members','project_orders.member_id','=','members.id')
            ->where(function($query)use($request){
                $order_id = $request->get('order_id');
                $project_id = $request->get('project_id');
                if($order_id) {
                    $query->where('order_id', '=', $order_id);
                }
                if($project_id) {
                    $query->where('project_id', '=', $project_id);
                }
           })->orderBy('created_at','desc')
            ->select('project_orders.*','members.phone')
            ->paginate(10);
        $project = Project::all();
        $orders->appends($request->all());
        $role_type = \Auth::guard('web')->user()->role_type;
        return view('admin.project.order', ['orders'=>$orders, 'project'=>$project,'role_type' => $role_type]);
    }

    public function change(Request $request, ProjectService $projectService)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        $order = ProjectOrder::find($id);
        if(!$order) {
            return ['code'=>201, 'message'=>'fail'];
        }
        if($status  == 2 && $order->member_id) {
            $projectService->transfer($order);
        }
        $order->status = $status;
        $order->save();
        return ['code'=>200, 'message'=>'success'];
    }

    public function getCreate()
    {
        return view('admin.project.create_order', ['projects'=>Project::all()->toArray()]);
    }

    public function create(Request $request, ProjectOrderRepository $projectOrderRepository)
    {
        $role_type = \Auth::guard('web')->user()->role_type;
        if($role_type != 1){
            return ['code'=> 201, 'data'=>'没有操作权限'];
        }
        $request->flash();
        $this->validate($request, [
            'project_id'=>'required',
            'phone'     =>'exists:members,phone',
            'price'     =>['required','regex:/^(0|[1-9][0-9]{0,9})(\.[0-9]{1,2})?$/'],
            'quantity'  =>['required', 'numeric', 'min:1']
        ]);

        $data = $request->all();
        $data['pay_type'] = 4;
        $data['status'] = 1;
        $projectOrderRepository->create($data);
        return redirect()->route('projectOrder');
    }
}
