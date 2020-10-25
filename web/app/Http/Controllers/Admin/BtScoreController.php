<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-01
 * Time: 16:17
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Artbc\BtScore;
use App\Model\Artbc\BtScoreLog;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Artbc\WalletInvite;
use App\Model\ArtbcLog;
use App\Model\Member;
use App\Model\OpeLog;
use App\Utils\ResUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BtScoreController extends Controller
{
    public function index()
    {
        $models = BtScoreLog::where(function ($query) {
            $phone = request()->get('phone');
            $type = request()->get('type');
            if ($phone) {
                $mid = Member::fetchIdWithPhone($phone);
                if ($mid) {
                    $query->where('member_id', $mid);
                }
            }
            if ($type) {
                $query->where('type', $type);
            }
        })->orderBy('id', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.btscore.index', compact('models'));
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
            $model = BtScoreLog::find($id);
            if (empty($model)) {
                return ResUtil::error(201, '数据不存在');
            }
            if ($model->type != BtScoreLog::TYPE_TIBI) {
                throw new \Exception('该交易不是提取');
            }
            $model->stat = $stat;
            $model->note = $note;
            $model->auditor = Auth()->id();
            if ($stat == BtScoreLog::STAT_REJECT) {
                // 如果是驳回
                BtScoreLog::add($model->member_id, abs($model->amount), BtScoreLog::TYPE_TIBI_REJECT, 1, $model->auditor, '');
            }else{
                // 如果是通过，计算手续费及购物积分
                $absAmount = abs($model->amount);
                $model->fee = round($absAmount * 0.05, 2);
                $model->shopping_score = round($absAmount * 0.15, 2);
                $btScore = BtScore::fetchByMemberId($model->member_id);
                $btScore->fee += $model->fee;
                $btScore->shopping_score += $model->shopping_score;
                if (!$btScore->save()){
                    throw new \Exception(__LINE__ . '服务器异常，请联系管理员');
                }
            }
            if (!$model->save()) {
                throw new \Exception(__LINE__ . '服务器异常，请联系管理员');
            }
            DB::commit();

            return ResUtil::ok();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());

            return ResUtil::error(201, $e->getMessage());

        }
    }

    // 统计当前团队长业绩, 邀请人数，一级
    public function sum(Request $request)
    {
        $phone = $request->get('phone');
        $user = Auth::user();
        empty($phone) && $phone = $user->phone;
        // 一级邀请人数
        // 二级邀请人数
        // 一级邀请人数购买数量
        // 二级邀请人数购买数量
        $member = Member::fetchModelByPhone($phone);
        if (!$member) {
            return '用户不存在';
        }
        $sons = WalletInvite::where('pid', $member->id)->get();
        $sonNum = count($sons);
        $sonBuy = 0;
        $sonOrderNum = 0;
        $sonLevel1 = [];
        foreach ($sons as $model) {
            $amount = BtScoreUnlock::where('member_id', $model->member_id)->sum('amount');
            $sonBuy += $amount;
//            $sonOrderNum += BtScoreUnlock::where('member_id', $model->member_id)->count('id');
            if ($model->level == 1){
                $sonLevel1[] = [
                    'phone' => Member::fetchPhoneWithId($model->member_id),
                    'amount' => $amount
                ];
            }
        }
        return view('admin.btscore.user_sum', compact('sonNum', 'sonBuy', 'sonLevel1'));
    }

}