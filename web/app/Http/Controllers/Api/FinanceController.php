<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-17
 * Time: 15:42
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Finance;
use App\Model\ListModel;
use App\Model\Member;
use App\Utils\ApiResUtil;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $member = Member::apiCurrent();
        $query = Finance::where('member_id', $member->id)
            ->where('asset_type', Account::BALANCE);
        $listModel = new ListModel($query);
        $models = $listModel->fetchModels([
             'id', 'member_id', 'type', 'content', 'balance', 'amount', 'after_amount', 'created_at'
        ]);
        $count = count($models);
        $list = [];
        foreach($models as $i => $model){
            /**
             * \ id: 1,
            balance: 100, // 记录后余额
            amount: 11,  // 变量数量
            type: 1,  // 变更类型
            typeLabel: '充值',  // 变更类型说明
             */
            $list[$i] = [
               'id' => $model->id,
                'balance' => $model->after_amount,
                'amount' => $model->balance,
                'type' => $model->type,
                'typeLabel' => $model->content,
                'created_at' => $model->created_at->toDateTimeString()
            ];
        }
        return ApiResUtil::ok([
            'hasMore' => intval($count == ApiResUtil::PAGENATION),
            'list' => $list
        ]);
    }
}