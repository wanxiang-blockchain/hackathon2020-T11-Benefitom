<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/7/9
 * Time: 下午2:34
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Artbc\BtScore;
use App\Model\Artbc\BtScoreLog;
use App\Model\ListModel;
use App\Model\Member;
use App\Utils\ApiResUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BtScoreController extends Controller
{

	const APPID = 'asdfasdlkIDDFsfdallisdfnlkasdf';

	public function info()
	{
		$member = Member::apiCurrent();
	}

	public function logs()
    {
        $member = Member::apiCurrent();
        $query = BtScoreLog::where('member_id', $member->id);
        $listModel = new ListModel($query);
        $models = $listModel->fetchModels(['id', 'type', 'amount', 'note', 'fee', 'balance', 'shopping_score', 'created_at']);
        $count = count($models);
        foreach ($models as $i => $v)
        {
            $models[$i]->typeLabel = BtScoreLog::fetchTypeLabel($v->type);
            unset($models[$i]->type);
        }
        return ApiResUtil::ok([
            'hasMore' => $count == ApiResUtil::PAGENATION,
            'list' => $models->toArray()
        ]);
    }

	public function ti(Request $request)
	{
	    return ApiResUtil::error('提取功能已迁至助推计划');
		$member = Member::apiCurrent();
		$amount = $request->get('amount');
		$card = $request->get('card');
        $name = $request->get('name');
        $bank = $request->get('bank');

		if (empty($amount) || empty($card) || empty($name) || empty($bank)) {
			return ApiResUtil::error('请输入提取信息');
		}

		if (empty($amount) || $amount < 200) {
			return ApiResUtil::error('单次兑换不得少于200');
		}

//		if (strtolower(substr($btaccount, 0, 2)) !== '0x' || strlen($eth_addr) !== 42) {
//			return ApiResUtil::error('钱包地址格式不正确');

//		}
		DB::beginTransaction();
		try {
			// 添加artbc_log，artbc
			$artbc = BtScore::fetchByMemberId($member->id);
			if (!$artbc || $artbc->score < $amount) {
				throw new TradeException('持币数量不够');
			}
			BtScoreLog::add($member->id, -1 * $amount, BtScoreLog::TYPE_TIBI, 0, '', '', $card, $name, $bank);
			\DB::commit();
			return ApiResUtil::ok();
		} catch (TradeException $e) {
			\DB::rollBack();
			return ApiResUtil::error( $e->getMessage());
		} catch (\Exception $e) {
			\Log::error($e->getTraceAsString());
			\DB::rollBack();
			return ApiResUtil::error( $e->getMessage());
		}

	}


}