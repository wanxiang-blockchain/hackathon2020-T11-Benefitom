<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Tender\Tender;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderFlow;
use App\Model\Tender\TenderGuess;
use App\Model\Tender\TenderLog;
use App\Model\Tender\TenderMsg;
use App\Utils\DateUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TenderWinner extends Command
{
	use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tender:winner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算暗标拍品奖项';

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
	    // 取出当计算奖项
	    try{
	    	$this->logstart();
		    DB::beginTransaction();
		    $tenders = Tender::where('tender_end', '<', DateUtil::now())->where(['type' => Tender::TENDER, 'stat' => Tender::STAT_ING,])->get();
		    $this->logmsg("start: get " . count($tenders) . " teners");
		    foreach ($tenders as $tender)
		    {
		    	$this->logmsg("start deal {$tender->code}");
			    // 置一下状态
			    $tender->stat = Tender::STAT_COUNT;
			    $tender->save();
			    // 计算第一名
			    $countGuess = $tender->guess_count;
			    // 更新下拍品状态
			    $tender->stat = Tender::STAT_GUESS_COUNT_FINISHED;
			    if(!$tender->save()){
				    throw new \Exception('数据库写入失败');
			    }
			    $this->logmsg('change tender stat to ' . Tender::STAT_GUESS_COUNT_FINISHED);
			    if($countGuess == 0){
				    $this->logmsg('no one guess');
				    // 如果没有人猜
				    continue;
			    }else {
				    // 一等奖数目
				    $firstPriceCount = intval($countGuess * 0.1);

				    // 取出成交价
				    $dealPrice = TenderLog::where('tender_id', $tender->id)
					    ->orderByDesc('price')->orderBy('created_at')
					    ->first();
				    if(empty($dealPrice)) {
					    $this->logmsg('no one tender');
					    // 如果没有人成交
					    continue;
				    }
				    // 取离成交价最近的最早出价的几位
				    $guesses = TenderGuess::where('tender_id', $tender->id)
					    ->orderByRaw("abs(tender_price - {$dealPrice->price})")->orderBy('created_at', 'asc')
					    ->limit($firstPriceCount + 1)->get();
				    foreach ($guesses as $index => $guess) {
					    if($index == 0) {
						    $this->logmsg("winner[{$guess->member_id}] tender[{$guess->tender_id}] guess[{$guess->id}]");
						    // 第一名，分30%奖金
						    // 修改tender_guess.winner_type  添加用户小红花，添加小红花流水
						    static::awards($guess, TenderGuess::PRICE_WINNER,
						        $guess->tender->firstPrice(), TenderFlow::TYPE_GUESS_PRIZE_WINNER);
					    }else {
						    $this->logmsg("first_price[{$guess->member_id}] tender[{$guess->tender_id}] guess[{$guess->id}]");
						    // 一等奖，均分50%奖金
						    static::awards($guess, TenderGuess::PRICE_FIRST,
							    round($guess->tender->secondPrice() / $firstPriceCount, 2), TenderFlow::TYPE_GUESS_PRIZE_FIRST);
					    }
				    }
			    }
			    $this->logmsg("finish deal {$tender->code}");
		    }// end foreach ($tenders as $tender)
		    DB::commit();
	    } catch (\Exception $e) {
			$this->logmsg($e->getTraceAsString());
			DB::rollBack();
	    }
    }

    public static function awards(TenderGuess $guess, $prieType, $priceMoney, $tenderFlow)
    {
    	// 改写 估价表
	    $guess->winner_type = $prieType;
	    if (!$guess->save()){
		    throw new \Exception('数据库写入失败');
	    }
	    // 添加资产
	    TenderAsset::add($guess->member_id, $priceMoney, $tenderFlow);
	    // 增加获奖表
	    \App\Model\Tender\TenderWinner::create([
		    'tender_id' => $guess->tender_id,
		    'winner_type' => $prieType,
		    'member_id' => $guess->member_id,
		    'bonus' => $priceMoney,
	    ]);
	    // 用户发消息
	    TenderMsg::setTempAward($guess->member_id, $guess->tender->name, $guess->winner_type, $priceMoney);
    }

}
