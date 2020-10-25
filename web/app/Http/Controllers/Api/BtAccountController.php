<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-22
 * Time: 09:57
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Btshop\Btaccount;
use App\Model\Member;
use App\Service\ValidatorService;
use App\Utils\ApiResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BtAccountController extends Controller
{
    public function bind(Request $request, ValidatorService $validatorService)
    {
        $data = $request->all();
        $validator = $validatorService->checkValidator([
            'account' => 'required|string',
            'name' => 'required|string',
        ], $data);
        if ($validator['code'] !== 200) {
            return ApiResUtil::error($validator['data']);
        }
        $member = Member::apiCurrent();
        DB::beginTransaction();
        try {
            $model = Btaccount::fetchModel($member->id);
            if (!$model) {
                $model = new Btaccount();
            }
            $model->member_id = $member->id;
            $model->account = $data['account'];
            $model->name = $data['name'];
            if (!$model->save()) {
                throw new TradeException('绑定失败');
            }
            DB::commit();
            return ApiResUtil::ok();
        } catch (TradeException $e) {
            DB::rollBack();
            return ApiResUtil::error($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return ApiResUtil::error($e->getMessage());
        }
    }

    public function index()
    {
        $member = Member::apiCurrent();
        $model = Btaccount::fetchModel($member->id);
        $data = $model ? $model->toArray() : [];
        return ApiResUtil::ok($data);

    }
}