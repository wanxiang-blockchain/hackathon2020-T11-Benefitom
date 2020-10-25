<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/27
 * Time: 下午5:28
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Asset;
use App\Model\AssetType;
use App\Model\Member;

class AssetController extends Controller
{
	public function index()
	{
		$models = Asset::where(function($query){
			$query->where('asset_type', '!=', 'T000000001');
			$request = request();
			$phone = $request->get('phone');
			$asset_type = $request->get('asset_type');
			$is_lock = $request->get('is_lock');
			if($phone) {
				$member = Member::where('phone' , $phone)->first();
				$query->where('account_id', $member->account->id);
			}
			if($asset_type) {
				$query->where('asset_type', $asset_type);
			}
			if($is_lock) {
				$query->where('is_lock', $is_lock);
			}
		})->orderBy('created_at','desc')->paginate(10);
		$models->appends(Request()->all());
		$assetTypes = AssetType::where('code', '!=', Account::BALANCE)->get();
		return view('admin.assets.index', compact('models', 'assetTypes'));
	}

}