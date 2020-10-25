<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Account;
use App\Model\Asset;
use App\Model\CostLog;
use App\Model\Member;
use App\Model\Score;
use App\Model\ScoreLog;
use App\Service\AccountService;
use App\Service\FinanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixScore extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	return 404;
        // 1、取出所有有卖出的用户
	    $sellers = DB::select('select seller_id from trade_logs group by seller_id');
	    $accountService = new AccountService();
	    foreach ($sellers as $seller) {
	    	$seller = $seller->seller_id;
	    	// 取出该用户信息
		    $member = Member::where('id', $seller)->first();
		    if(empty($member)) {
		    	echo "empty user $seller" . PHP_EOL;
		    	continue;
		    }
		    //  交易之前的持有成本、持仓及积分
		    list($hold_price, $hold_amount) = $this->countFin($member);
		    $score = 0;
		    // 取出所有交易
		    $tradeLogs = DB::select('select * from trade_logs where buyer_id = ? or seller_id = ?', [$seller, $seller]);
		    foreach($tradeLogs as $trade) {
		    	if($trade->buyer_id == $seller) {
		    		// 如果是买入，计算其新成
					$hold_price = $this->countCost($hold_price, $hold_amount, $trade->price, $trade->amount);
					$hold_amount += $trade->amount;
					echo $member->phone . " [" . $trade->created_at . "] 以 " . $trade->price . " 买入：" . $trade->amount . " 成本变更为：$hold_price 持仓：$hold_amount" . PHP_EOL;
			    }

			    if($trade->seller_id == $seller) {
		    		$profilt = $this->profit($hold_price, $trade->price, $trade->amount);
		    		if($profilt > 0) {
		    			$score += round($profilt * 0.2, 2);
				    }
				    $hold_amount -= $trade->amount;
				    echo $member->phone . " [" . $trade->created_at . "] 以 " . $trade->price . " 卖出：" . $trade->amount . " 累计积分达：$score 成本变更为：$hold_price 持仓：$hold_amount" . PHP_EOL;
			    }
		    }
		    if($hold_amount == 0) {
		    	$hold_price = 0;
		    }
		    echo $member->phone . " final [{$seller}] 新算法持仓：" . $hold_amount . " 成本：" . $hold_price . " 积分：" . $score . PHP_EOL;
		    $account = Account::where('member_id', $member->id)->first();
		    $scoreModel = Score::where('account_id', $account->id)->first();
		    $assets = $this->nowAsset($account->id);
		    echo $member->phone . " final [{$seller}] 目前持仓：" . $assets[0]. " 成本：" . $assets[1] . " 积分：" . $scoreModel->score . "\n\n";
		    // 修改数据，finances, cost_log, score, score_log, asset
			DB::beginTransaction();
			$wucha = round($scoreModel->score - $score, 2);
			if($wucha > 0) {
				echo $member->phone . " change [{$seller}] 需要变更积分\n";
				$accountService->addAsset($account->id, Account::BALANCE, $wucha);
				// 增加余额
				FinanceService::record($member->id, 'T000000001', 1, $wucha, 0, "积分规则变更|转化积分{$wucha}为余额");
				// 变更积分
				$scoreModel->score = $score;
				$scoreModel->save();
				ScoreLog::create([
					'score' => -1 * $wucha,
					'account_id' => $account->id,
					'order_id' => 0,
					'type' => 3
				]);
			}

		    if($hold_price != $assets[1]) {
			    echo $member->phone . " change [{$seller}] 需要变更成本\n";
			    // 所有修改成本
			    DB::table('assets')->where('account_id', $account->id)
				    ->where('asset_type', '017001')
				    ->update(['cost' => $hold_price]);

			    CostLog::record($account->id, $hold_amount, '017001', $hold_price, "变更积分规则，计算成本为：{$hold_price}");
		    }
		    DB::commit();
	    }
    }

    public function nowAsset($account_id) {
	    $asset = Asset::where('account_id', $account_id)->where('asset_type', '017001')->where('is_lock', 0)->first();
	    if($asset) {
	    	return [$asset->amount, $asset->cost];
	    }
	    return [0, 0];
    }

	public function profit($hold_price, $sale_price, $sale_amount)
	{
		$poundage = round($sale_amount * $sale_price * 0.003, 2);
		echo "$sale_price 卖出 $sale_amount 产生交易手续费 $poundage" . PHP_EOL;
		return ($sale_price - $hold_price) * $sale_amount - $poundage;
	}

    public function countCost($hold_price, $hold_amount, $buy_price, $buy_amount)
    {
	    return round(($hold_price * $hold_amount + $buy_price * $buy_amount) / ($hold_amount + $buy_amount), 2);
    }

	/**
	 * 计算初始成本及持仓
	 * @desc countFin
	 * @param $member_id
	 */
    public function countFin($member)
    {
		$finances = DB::select('select * from finances where member_id = ? and created_at < "2017-08-08 09:00:00" and type in (1, 3) and asset_type = ?', [$member->id, '017001']);
		if (empty($finances)) {
			return [0, 0];
		}
		$hold_amount = 0;
		$hold_price = 0;
		foreach ($finances as $finance) {
			$buy_price = 98;
			if($finance->type == 3 && $finance->created_at > '2017-07-18 14:39:00') {
				$buy_price = 100;
			}
			$buy_amount = abs($finance->balance / $buy_price);
			if($hold_amount + $buy_amount == 0) {
				var_dump($finance);
			}
			$hold_price = round(($hold_price * $hold_amount + $buy_price * $buy_amount) / ($hold_amount + $buy_amount), 2);
			$hold_amount += $buy_amount;
			echo $member->phone . " [" . $finance->created_at . "] 以 $buy_price /幅 " . $finance->content . "，持仓：[" . $hold_amount . "] 成本：[" . $hold_price . "]" . PHP_EOL;
		}
		return [$hold_price, $hold_amount];
    }
}
