<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-10
 * Time: 10:51
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\Artbc\Action;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use Illuminate\Http\Request;

class CcaController extends Controller
{
    public function index(Request $request, ValidatorService $validatorService)
    {
        $data = $request->all();
        $validator = $validatorService->checkValidator([
            'token' => 'required|string',
            'account' => 'required|string',
            'type' => 'required|string|in:all,in,out',
            'page' => 'required|int'
        ], $data);
        if ($validator['code'] !== 200){
            return ApiResUtil::error($validator['data']);
        }
        $account = $data['token'] == 'CCA' ? 'eosio.token' : 'shaodetang12';
        $query = Action::where('account', $account);
        switch ($data['type']){
            case 'in':
                $query->where('to', $data['account']);
                break;
            case 'out':
                $query->where('from', $data['account']);
                break;
            default:
                $query->where('from', $data['account'])
                    ->orWhere('to', $data['account']);
                break;
        }
        $models = $query->groupBy(['block_num', 'trxid'])
            ->offset($data['page'] * ApiResUtil::PAGENATION)
            ->limit(ApiResUtil::PAGENATION)
            ->orderByDesc('timestamp')
            ->get();
        $count = count($models);
        return ApiResUtil::ok([
            'hasMore' => $count == ApiResUtil::PAGENATION,
            'list' => $models->toArray()
        ]);
    }
}