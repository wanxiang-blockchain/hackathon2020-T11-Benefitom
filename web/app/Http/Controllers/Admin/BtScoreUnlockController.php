<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2018-12-01
 * Time: 16:17
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Artbc\BtConfig;
use App\Model\Artbc\BtScore;
use App\Model\Artbc\BtScoreLog;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Member;
use App\Model\OpeLog;
use App\Model\User;
use App\Utils\DissysPush;
use App\Utils\ResUtil;
use Illuminate\Support\Facades\DB;
use App\Service\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BtScoreUnlockController extends Controller
{
    public function index()
    {
        $models = BtScoreUnlock::where(function ($query) {
            $phone = request()->get('phone');
            if ($phone) {
                $mid = Member::fetchIdWithPhone($phone);
                if ($mid) {
                    $query->where('member_id', $mid);
                }
            }
        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.btscoreunlock.index', compact('models'));
    }

    public function create()
    {
        return view('admin.btscoreunlock.create');
    }

    public function unlockEdit(Request $request, ValidatorService $validatorService)
    {
        if (($request->isMethod('GET'))) {
            return view('admin/artbcunlock/edit');
        } else {
            $user_id = Auth()->id();
            $user = User::find($user_id);
            if(!in_array($user['role_type'], [1, 7])){
                return ['code'=>250,'data'=>'没有操作权限'];
            }
            $data = $request->all();
            $validator = $validatorService->checkValidator([
                'phone' => 'required',
                'amount' => 'required',
//                'unlock_period' => 'required'
            ], $data);

            if ($validator['code'] !== 200) {
                return $validator;
            }

            $member = Member::fetchModelByPhone($data['phone']);
            if (!$member) {
                return ['code' => 203, 'data' => '会员不存在'];
            }

            /**
             * 	每个用户每天可入单数.  分配时取用 todo
            每个用户每单限额.        分配时取用 todo
            系统每天总售单数.        分配时取用 todo
             */
            DB::beginTransaction();
            try{

                $btconfig = BtConfig::fetchOne();
                if ($btconfig) {
                    $perOrderCount = BtScoreUnlock::where('member_id', $member->id)
                        ->where('created_at', '>', date('Y-m-d 00:00:00'))
                        ->count('id');
                    if ($perOrderCount >= $btconfig->per_order_nums) {
                        throw new TradeException('单人单日不可超过' . $btconfig->per_order_nums . '单');
                    }
                    if ($data['amount'] > $btconfig->per_order_amount * 2) {
                        throw new TradeException('单人单日额度不可超过' . $btconfig->per_order_amount * 2 . '');
                    }
                    $totalOrderCount = BtScoreUnlock::where('created_at', '>', date('Y-m-d 00:00:00'))
                        ->count('id');
                    if ($totalOrderCount >= $btconfig->total_order_nums) {
                        throw new TradeException('系统单日总单数不得超过' . $btconfig->total_order_nums. '单');
                    }
                }

                $user_id = Auth()->id();

                $data['creator'] = $user_id;
                $data['member_id'] = $member->id;
                $data['unlock_period'] = 1;
                $data['percent'] = 2;
                $data['stat'] = BtScoreUnlock::STAT_DONE;
                $data['order_code'] = BtScoreUnlock::orderMake();
                if (!BtScoreUnlock::create($data)) {
                    throw new TradeException('数据保存失败');
                }
//                if (!DissysPush::score($data['order_code'], $member->phone, $data['amount'])){
//                    throw new TradeException('推送积分系统失败');
//                }
                DB::commit();
                return ResUtil::ok();
            }catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return ResUtil::error(201, $e->getMessage());
            }

        }
    }


    public function del($id)
    {
        $user_id = Auth()->id();$user = User::find($user_id);
        if(!in_array($user['role_type'], [1, 7])){
            return ['code'=>250,'data'=>'没有操作权限'];
        }

        DB::beginTransaction();
        try {

            $model = BtScoreUnlock::where('id', $id)->first();
            if (!$model) {
                return ['code' => 201, 'data' => '代理人不存在'];
            }

            $unloked = $model->unlocked_amount;
            if ($model->unlocked_amount > 0) {
                BtScoreLog::add($model->member_id,  -1 * $unloked,BtScoreLog::TYPE_SYSTEM_REVERT);
            }

            $member = Member::find($model->member_id);
            $parent = Member::walletParent($member->wallet_invite_member_id);
            if ($parent) {
                // 上级直接奖励5%
                $amountPrized =  round($model->amount * 0.025, 2);
                    BtScoreLog::add($parent->id,  -1 * $amountPrized,BtScoreLog::TYPE_SYSTEM_REVERT);
                $pParent = Member::walletParent($parent->wallet_invite_member_id);
                if ($pParent && Member::walletInviteSum($pParent->id) >= 3){
                    $amountPrized =  round($model->amount * 0.01, 2);
                    BtScoreLog::add($pParent->id,  -1 * $amountPrized,BtScoreLog::TYPE_SYSTEM_REVERT);
                }
            }

            BtScoreUnlock::where('id', $id)
                ->delete();

            OpeLog::record('删除版通锁仓' . $model->member->phone, ['phone' => $model->member->phone, 'creator' => $model->creator], $model->member->phone);

            DB::commit();
            return ['code' => 200, 'data' => '成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ResUtil::error(201, $e->getMessage());
        }

    }
}