<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/2
 * Time: 下午1:33
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Agent;
use App\Model\Member;
use App\Model\OpeLog;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{

	public function index()
	{
		$models = Agent::where(function($query){
			$phone = request()->get('phone');
			if($phone) {
				$query->where('phone', $phone);
			}
		})->orderBy('created_at','desc')->paginate(10);
		$models->appends(Request()->all());
		return view('admin.agent.index', compact('models'));
	}

	public function create()
	{
		return view("admin.agent.create");
	}

	public function postCreate(Request $request, ValidatorService $validatorService)
	{
		$data = $request->all();
		$validator = $validatorService->checkValidator([
			'phone' => 'unique:agents|digits:11'
		], $data);

		if ($validator['code'] !== 200) {
			return $validator;
		}

		if (!Member::where('phone', $data['phone'])->exists()) {
			return ['code' => 203, 'data' => '会员不存在'];
		}

		$user_id = Auth()->id();

		$data['creator'] = $user_id;

		if(Agent::create($data)) {
			return ['code' => 200, 'data' => '成功'];
		}

		return ['code' => 201, 'data' => '失败'];

	}

	public function delete($id)
	{
		$agent = Agent::where('id', $id)->first();
		if(!$agent){
			return ['code' => 201, 'data' => '代理人不存在'];
		}

		Agent::where('id', $id)
			->delete();

		OpeLog::record('删除代理人' . $agent->phone, ['phone' => $agent->phone, 'creator' => $agent->creator], $agent->phone);

		return ['code' => 200, 'data' => '成功'];
	}

	public function detail($phone)
	{
		if(empty($phone)) {
			return 404;
		}
		$id = Member::fetchIdWithPhone($phone);
		$models = Member::where(function($query) use ($id){
			$query->where('invite_member_id', $id);
		})->orderBy('created_at','desc')->paginate(10);

		$sum = DB::select('select count(1) as total from members where invite_member_id = ?', [$id]);
		$total = $sum[0]->total;

		return view('admin.agent.detail', compact('models', 'total'));
	}
}