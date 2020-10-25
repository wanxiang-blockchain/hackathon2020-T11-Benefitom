<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/11
 * Time: 下午5:18
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Delivery;
use App\Model\Member;
use App\Service\AccountService;
use App\Utils\ResUtil;
use ClassesWithParents\D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
	public function index(Request $request)
	{
		$phone = $request->get('phone');
		$stat = $request->get('stat');
		$beginTime = $request->get('beginTime');
		$endTime = $request->get('endTime');
		$query = Delivery::orderBy('id', 'desc');
		if($phone){
			$member = Member::where('phone', $phone)->first();
			if($member) {
				$query->where('member_id', $member->id);
			}
		}
		if($stat) {
			$query->where('stat', $stat);
		}
		if($beginTime) {
			$query->where('created_at', '>=', $beginTime);
		}
		if($endTime) {
			$query->where('created_at', '<=', $endTime);
		}
		$models = $query->paginate(10);
		return view('admin.delivery.index',compact('models'));
	}

	public function audit($id, Request $request, AccountService $accountService)
	{
		if(empty($request->input('note'))){
			return ResUtil::error(201, '您未填入快递单号');
		}
//		if(\Auth::guard('web')->user()->role_type != 3) {
//			return ['code'=>201, 'data'=>'无权限操作'];
//		}
		DB::beginTransaction();
		try{
			$model = Delivery::find($id);
			if (!$model) {
				return ResUtil::error(201, '数据不存在');
			}
			if ($model->stat != Delivery::STAT_SUBMIT) {
				return ResUtil::error(201, '请勿重复提交');
			}
			$model->stat = Delivery::STAT_DELIVEIED;
			$model->note = $request->input('note');
			$model->auditor = \Auth::user()->id;
			if(!$model->update()) {
				throw new \Exception('操作失败');
			}
			DB::commit();
			return ResUtil::ok();
		}catch (\Exception $e){
			DB::rollBack();
			\Log::error($e->getTraceAsString());
			return ResUtil::error(201, $e->getMessage());
		}
	}

	public function reject($id, Request $request, AccountService $accountService)
	{
		if(empty($request->input('note'))){
			return ResUtil::error(201, '您未填入快递单号');
		}
//		if(\Auth::guard('web')->user()->role_type != 3) {
//			return ['code'=>201, 'data'=>'无权限操作'];
//		}
		DB::beginTransaction();
		try{
			$model = Delivery::find($id);
			if (!$model) {
				return ResUtil::error(201, '数据不存在');
			}
			if ($model->stat != Delivery::STAT_SUBMIT) {
				return ResUtil::error(201, '请勿重复提交');
			}
			$model->stat = Delivery::STAT_REJECTED;
			$model->note = $request->input('note');
			$model->auditor = \Auth::user()->id;
			if(!$model->update()) {
				throw new \Exception('操作失败');
			}
			$accountService->addAsset($model->member->account->id, $model->asset_code, $model->amount);
			DB::commit();
			return ResUtil::ok();
		}catch (\Exception $e){
			DB::rollBack();
			\Log::error($e->getTraceAsString());
			return ResUtil::error(201, $e->getMessage());
		}
	}

	public function note($id, Request $request) {
		if(empty($request->input('note'))){
			return ResUtil::error(201, '您未填入备注');
		}
//		if(\Auth::guard('web')->user()->role_type != 3) {
//			return ['code'=>201, 'data'=>'无权限操作'];
//		}
		$model = Delivery::find($id);
		if(!$model) {
			return ResUtil::error(201, '数据不存在');
		}
		$model->note = $request->input('note');
		$model->auditor = \Auth::user()->id;
		if($model->update()) {
			return ResUtil::ok();
		}
		return ResUtil::error(201, '数据不存在');
	}
}