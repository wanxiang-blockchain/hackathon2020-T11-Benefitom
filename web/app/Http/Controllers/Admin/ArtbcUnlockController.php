<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/8/17
 * Time: 下午4:01
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Artbc\ArtbcUnlock;
use App\Model\Member;
use App\Model\OpeLog;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArtbcUnlockController extends Controller
{

	public function index()
	{
		$models = ArtbcUnlock::where(function($query){
			$phone = request()->get('phone');
			if($phone) {
				$query->where('phone', $phone);
			}
		})->orderBy('created_at','desc')->paginate(10);
		$models->appends(Request()->all());
		return view('admin.artbcunlock.index', compact('models'));
	}

	public function create()
	{
		return view('admin/artbcunlock/create');
	}

	public function edit(Request $request, ValidatorService $validatorService)
	{
		if (($request->isMethod('GET'))){
			return view('admin/artbcunlock/edit');
		}else{
			$data = $request->all();
			$validator = $validatorService->checkValidator([
				'phone' => 'required',
				'amount' => 'required',
				'unlock_period' => 'required',
                'unlock_times' => 'required',
                'start_unlock_day' => 'required',
			], $data);

			if ($validator['code'] !== 200) {
				return $validator;
			}

			$member = Member::fetchModelByPhone($data['phone']);
			if (!$member) {
				return ['code' => 203, 'data' => '会员不存在'];
			}

			$user_id = Auth()->id();

			$data['creator'] = $user_id;
			$data['member_id'] = $member->id;

			if(ArtbcUnlock::add($data)) {
				return ['code' => 200, 'data' => '成功'];
			}

			return ['code' => 201, 'data' => '失败'];
		}
	}


	public function del($id)
	{

		DB::beginTransaction();
		try{

			$model = ArtbcUnlock::where('id', $id)->first();
			if(!$model){
				return ['code' => 201, 'data' => '代理人不存在'];
			}

			if ($model->unlocked_amount > 0) {
				return ResUtil::error(201, '该锁仓已有释放，删除请联系管理员');
			}

			ArtbcUnlock::where('id', $id)
				->delete();

			OpeLog::record('删除锁仓' . $model->member->phone, ['phone' => $model->member->phone, 'creator' => $model->creator], $model->member->phone);

			DB::commit();
			return ['code' => 200, 'data' => '成功'];
		}catch (\Exception $e){
			DB::rollBack();
			return ResUtil::error(201, $e->getMessage());
		}
	}
}