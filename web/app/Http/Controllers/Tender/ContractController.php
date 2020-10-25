<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/26
 * Time: 上午9:27
 */

namespace App\Http\Controllers\Tender;


use App\Http\Controllers\Controller;
use App\Model\Member;
use App\Model\Tender\TenderBroadcastRead;
use App\Utils\ResUtil;
use Illuminate\Http\Request;

class ContractController extends Controller
{

	public function index()
	{
		return view('front.tender.contract');
	}

	public function agree($flag, Request $request)
	{
		$broadcast_id = $request->get('id');
		$member = Member::current();
		if($flag == 'agree'){
			TenderBroadcastRead::read($member->id, $broadcast_id);
			return ResUtil::ok();
		}
		return ResUtil::error();
	}

}