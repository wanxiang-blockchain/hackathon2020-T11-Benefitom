<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/4/26
 * Time: 上午11:01
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\ArtbcLog;
use App\Model\OpeLog;
use App\Utils\QueryUtil;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Model\Member;

class ArtbcController extends Controller
{

	public function logs(Request $request)
	{
		$models = ArtbcLog::where(function($query)use($request) {
			$data = $request->all();
			$phone = $request->get('phone');
			if($phone){
				$member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
				$query->whereIn('member_id', $member);
			}
			QueryUtil::selectBuild($query, $data, [
				['beginTime', '>=', 'created_at'],
				['endTime', '>=', 'created_at'],
			]);
		})->orderBy('id','desc')->paginate(10);
		return view('admin.artbc.logs', compact('models'));
	}

	public function ti(Request $request)
	{
		$models = ArtbcLog::where(function($query)use($request) {
			$query->where('type', ArtbcLog::TYPE_TIBI);
			$data = $request->all();
			$phone = $request->get('phone');
			if($phone){
				$member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
				$query->whereIn('member_id',$member);
			}
			QueryUtil::selectBuild($query, $data, [
				['beginTime', '>=', 'created_at'],
				['endTime', '>=', 'created_at'],
			]);
		})->orderBy('id','desc')->paginate(10);
		return view('admin.artbc.ti', compact('models'));
	}
	
	public function audit(Request $request)
	{
		$id = $request->get('id');
		$stat = $request->get('stat');
		$note = $request->get('note');
		if (!in_array($stat, [1, 2])) {
			return ResUtil::error('参数错误');
		}
		DB::beginTransaction();
		try {
			$model = ArtbcLog::find($id);
			if (empty($model)) {
				return ResUtil::error(201, '数据不存在');
			}
			if ($model->type != ArtbcLog::TYPE_TIBI) {
				throw new \Exception('该交易不是提取');
			}
			$model->stat = $stat;
			$model->note = $note;
			$model->auditor = Auth()->id();
			if (!$model->save()) {
				throw new \Exception('服务器异常，请联系管理员');
			}
			if ($stat == ArtbcLog::STAT_REJECT) {
				// 如果是驳回
				ArtbcLog::add($model->member_id, abs($model->amount), ArtbcLog::TYPE_TIBI_REJECT);
			}
			DB::commit();

			return ResUtil::ok();
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());

			return ResUtil::error(201, $e->getMessage());

		}
	}
}