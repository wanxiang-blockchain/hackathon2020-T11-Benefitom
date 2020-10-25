<?php

namespace App\Http\Controllers\Front;

use App\Model\Artbc\Addr;
use App\Model\Member;
use App\Service\ValidatorService;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AddrController extends Controller
{
    //
	public function index()
	{
		$member = Member::current();
		$addr = Addr::fetchByMemberId($member->id);
		$prev_action = request()->get('prev_action', '');
		if ($prev_action){
			$prev_action = urldecode($prev_action);
		}else{
			$prev_action = 'member';
		}
		return view('front.member.center.addr', compact('addr', 'prev_action'));
	}

	public function edit(Request $request, ValidatorService $validatorService)
	{
		$member = Member::current();
		$rule = [
			'name' => 'required',
			'province' => 'required',
			'city' => 'required',
			'area' => 'required',
			'addr' => 'required',
			'phone' => 'required|numeric',
		];
		$data = $request->all();
		$validator = $validatorService->checkValidator($rule, $data);
		if ($validator['code'] !== 200) {
			return $validator;
		}
		$model = Addr::fetchByMemberId($member->id);
		if (!$model) {
			$model = new Addr();
		}
		$data['member_id'] = $member->id;
		$model->fill($data);
		if ($model->save()){
			return ResUtil::ok();
		}
		return ResUtil::error('数据保存失败');
	}
}
