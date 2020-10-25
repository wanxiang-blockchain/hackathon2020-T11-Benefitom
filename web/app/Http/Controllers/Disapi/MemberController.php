<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-06
 * Time: 20:09
 */

namespace App\Http\Controllers\Disapi;


use App\Http\Controllers\Controller;
use App\Model\Artbc\BtScore;
use App\Model\Member;
use App\Utils\ApiResUtil;
use App\Utils\DisVerify;
use Illuminate\Http\Request;
use Twilio\Rest\Api;

class MemberController extends Controller
{

    public function members(Request $request)
    {
        $page = $request->get('page', 0);
        $models = Member::select(['id', 'phone', 'wallet_invite_member_id'])
            ->orderBy('id')
            ->offset($page * 100)
            ->limit(100)
            ->get();
        $list = [];
        foreach ($models as $model)
        {
            $score = BtScore::where('member_id', $model->id)->first();
            $list[] = [
                'phone' => $model->phone,
                'up_phone' => strval(Member::fetchPhoneWithId($model->wallet_invite_member_id)),
                'left_coin' => $score ? $score->score : 0,
                'use_coin' => $score ? $score->shopping_score : 0
            ];
        }
        return ApiResUtil::ok([
            'hasMore' => intval(count($list) == 100),
            'list' => $list
        ]);
    }

    public function member(Request $request)
    {
        $id = $request->get('id');
        if (empty($id) || !is_integer($id)){
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        $member = Member::where('id', $id)
            ->select('id', 'phone', 'wallet_invite_member_id')
            ->first();
        if (!$member) {
            return ApiResUtil::error(ApiResUtil::NO_DATA);
        }
        return ApiResUtil::ok([
            'phone' => $member->phone,
            'id' => $member->id,
            'pid' => $member->wallet_invite_member_id
        ]);
    }

    public function verify(Request $request)
    {
        $ticket = $request->get('ticket');
        $member = DisVerify::verifyTk($ticket);
        if ($member){
            return ApiResUtil::ok([
                'phone' => $member->phone,
            ]);
        }
        return ApiResUtil::error('wrong ticket');
    }

}