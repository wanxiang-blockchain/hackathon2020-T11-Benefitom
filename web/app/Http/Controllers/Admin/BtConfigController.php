<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-03
 * Time: 21:07
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Artbc\BtConfig;
use App\Utils\ResUtil;
use Illuminate\Http\Request;

class BtConfigController extends Controller
{
    public function edit(Request $request)
    {
        if ($request->isMethod('GET')){
            $model = BtConfig::fetchOne();
            return view('admin.btscore.config', compact('model'));
        } else{
            /**
             * 'total_order_nums',
            'per_order_nums',
            'per_order_amount',
            'percent'
             */
            $model = BtConfig::fetchOne();
            $total_order_nums = $request->get('total_order_nums');
            $per_order_nums = $request->get('per_order_nums');
            $per_order_amount = $request->get('per_order_amount');
            $percent = $request->get('percent');
            $period = $request->get('period');
            if (empty($total_order_nums) || empty($per_order_nums) || empty($per_order_amount) || empty($percent) || empty($period)) {
                return ResUtil::error(201, '数据不得为空');
            }
            $model->percent = $percent;
            $model->total_order_nums = $total_order_nums;
            $model->per_order_amount = $per_order_amount;
            $model->per_order_nums = $per_order_nums;
            $model->period = $period;
            $model->save();
            return ResUtil::ok();
        }
    }
}