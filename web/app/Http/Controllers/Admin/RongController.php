<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/27
 * Time: 下午7:34
 */

namespace App\Http\Controllers\Admin;


use App\Model\Account;
use App\Model\Finance;
use App\Model\Member;
use App\Model\Product;
use App\Model\OpeLog;
use App\Model\User;
use App\Model\UserProduct;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Service\ValidatorService;
use Illuminate\Http\Request;
use Mockery\Exception;

class RongController extends AdminController
{

	public function index()
	{
		$models = Product::all();
		return view('admin.rong.index', compact('models'));
	}

	public function userProduct()
	{
		$models = UserProduct::where(function($query){
			$phone = request()->get('phone');
			if($phone) {
				$id = Member::fetchIdWithPhone($phone);
				if($id) {
					$query->where('member_id', $id);
				}
			}
		})->orderBy('created_at','desc')->paginate(10);
		$models->appends(Request()->all());
		return view('admin.rong.user_product', compact('models'));
	}

	public function createProduct()
	{
		return view('admin.rong.create_product');
	}

	public function postProduct(Request $request, ValidatorService $validatorService)
	{
		$data = $request->all();
		$validator = $validatorService->checkValidator([
			'name' => 'required',
			'price' => 'required|min:1|numeric',
			'duration' => 'required|min:1|numeric',
			'rate' => 'required|max:0.99|min:0.01|numeric',
//			'amount' => 'required|min:1|numeric',
			'enable' => 'required|boolean',
			'info' => 'required',
			'picture' => 'required|image',
			'banner' => 'required|image',
		], $data);

		if ($validator['code'] !== 200) {
			return $validator;
		}

		$data['picture'] = $request->file('picture')->store('/public/project', 'public');
		$data['banner'] = $request->file('banner')->store('/public/project', 'public');

		if(Product::create($data)) {
			OpeLog::record('添加理财产品' . $data['name'], $data['name']);
			return ['code' => 200, 'data' => '成功'];
		}

		return ['code' => 201, 'data' => '失败'];

	}

	public function delete($id)
	{
		$model = Product::where('id', $id)->first();
		if(empty($model)) {
			return $this->success();
		}

		if($model->disabled()){
			return $this->error('开启状态不可删除');
		}

		if($model->delete()) {
			OpeLog::record('删除产品' . $id, ['name' => $model->name], $id);
			return $this->success();
		}
		return $this->error();
	}

	public function edit($id)
	{
		$model = Product::where('id', $id)->first();
		return view('admin.rong.edit_product', compact('model'));
	}

	public function postEdit(Request $request, ValidatorService $validatorService)
	{
		$data = $request->all();
		$rule = [
			'name' => 'required',
			'price' => 'required|min:1|numeric',
			'duration' => 'required|min:1|numeric',
			'rate' => 'required|max:0.99|min:0.01|numeric',
//			'amount' => 'required|min:1|numeric',
			'enable' => 'required|boolean',
			'info' => 'required',
			'id' => 'required|numeric',
			'banner' => 'image',
			'picture' => 'image',
		];
		$validator = $validatorService->checkValidator($rule, $data);

		if ($validator['code'] !== 200) {
			return $validator;
		}

		$model = Product::where('id', $data['id'])->first();
		if(empty($model)) {
			return $this->error('数据不存在');
		}

		// 有人购买不可更新rate
		if($model->rate != $data['rate'] && $model->sold_amount > 0) {
			return $this->error('已有销售份额，不可更改收益率！');
		}

		// 有人购买不可更新enable
		if($data['enable'] == Product::DISABLE && $model->sold_amount > 0) {
			return $this->error('已有销售份额，不可关闭！');
		}

		!empty($data['picture']) && $data['picture'] = $request->file('picture')->store('/public/project', 'public');
		!empty($data['banner']) && $data['banner'] = $request->file('banner')->store('/public/project', 'public');

		foreach ($rule as $key => $value) {
			!empty($data[$key]) && $model->$key = $data[$key];
		}

		if($model->update()) {
			OpeLog::record('修改理财产品' . $data['name'], $data, $model->id);
			return ['code' => 200, 'data' => '成功'];
		}

		return ['code' => 201, 'data' => '失败'];

	}

	public function endList()
	{
		$models = UserProduct::where(function($query){
			$phone = request()->get('phone');
			if($phone) {
				$id = Member::fetchIdWithPhone($phone);
				if($id) {
					$query->where('member_id', $id);
				}
			}
			$query->where('end_at', '<', date('Y-m-d H:i:s'));
			$query->where('stat', Product::STAT_HOLD);
		})->orderBy('created_at','desc')->paginate(10);
		$models->appends(Request()->all());
		return view('admin.rong.end_list', compact('models'));
	}

	public function endAudit($id, AccountService $accountService)
	{
		$user_id = Auth()->id();
		$user = User::find($user_id);
		if($user['role_type'] != 3){
			return ['code'=>250,'data'=>'没有操作权限'];
		}

		$model = UserProduct::find($id);
		// 是否到期
		if ($model->end_at > date('Y-m-d H:i:s')) {
			return $this->error('持有期尚未结束');
		}

		try{

			$member = $model->member;

			\DB::beginTransaction();
			// 修改购买状态
			$model->stat = Product::STAT_END;
			if (!$model->update()) {
				throw new \Exception('修改购买状态失败');
			}

			// 返余额
			$accountService->addAsset($member->account->id, Account::BALANCE, $model->earnings(), '');

			$r = FinanceService::record($member->id, Account::BALANCE, Finance::RONG_RETURN,
				$model->earnings(), 0, "购买{$model->product->name}返利{$model->earnings()}元");
			if (!$r) {
				throw new \Exception("数据库写入失败");
			}

			// 操作日志
			OpeLog::record("审核艺融宝返利:{$model->id}", [], $model->id);

			\DB::commit();
			return $this->success();

		}catch (\Exception $e) {
			\Log::error($_REQUEST);
			\Log::error($e->getTraceAsString());
			\DB::rollBack();
			return ['code'=>202, 'data'=>$e->getMessage()];
		}

	}

}