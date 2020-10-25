<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-11-28
 * Time: 21:11
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\Artbc\MsgRead;
use App\Model\Cms\Push;
use App\Model\Member;
use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JPush\Exceptions\APIRequestException;
use Twilio\Rest\Api;

class PushController extends Controller
{
    public function index(Request $request)
    {
        $member = Member::apiCurrent();
        $lastId = $request->get('lastId', 0);
        $query = Push::query();
        $query->where('stat', 1);
        $query->whereRaw("push_to in (0, " . $member->phone . ")");
//        if ($lastId > 0){
//            $query->where('id','>', $lastId);
//        }
        $query->where('push_at', '>=', date('Y-m-d H:i:s', time() - 86400 * 3));
        $models = $query->select(['id', 'con', 'title', 'subtitle', 'push_at'])->orderByDesc('push_at')->limit(10)->get();
        foreach ($models as $i => $v) {
            $lastId = $v->id;
            $models[$i]['readed'] = MsgRead::readed($member->id, $v->id);
        }
        return ApiResUtil::ok([
            'hasMore' => intval(count($models) == 10),
            'list' => $models,
            'lastId' => $lastId
        ]);
    }

    public function detail($id)
    {
        $model = Push::find($id);
        Log::debug($model);
        if (empty($model)){
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        $member = Member::apiCurrent();
        if ($model->push_to != 0 && $model->push_to !== $member->phone){
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        MsgRead::create([
            'push_id' => $model->id,
            'member_id' => $member->id
        ]);
        return ApiResUtil::ok([
            'id' => $id,
            'title' => $model->title,
            'subtitle' => $model->subtitle,
            'con' => $model->con,
            'push_at' => $model->push_at->toDateTimeString()
        ]);
    }

    public function read(Request $request)
    {
        $member = Member::apiCurrent();
        $push_id = $request->get('push_id');
        if ($push_id && !MsgRead::readed($member->id, $push_id)) {
             MsgRead::create([
                 'member_id' => $member->id,
                 'push_id' => $push_id
             ]);
        }
        return ApiResUtil::ok();
    }
}