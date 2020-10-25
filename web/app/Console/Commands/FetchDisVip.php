<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Artbc\BtScoreUnlock;
use App\Model\Artbc\WalletInvite;
use App\Model\Member;
use Illuminate\Console\Command;

class FetchDisVip extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:disvip';

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
        $phones =  [
            ['18050129549', 100],
            ['18682214499', 100],
            ['15171634916', 100],
        ];
        $list = [];
        foreach ($phones as $value){
            $phone = $value[0];
            $this->logmsg('start with phone ' . $phone);
            $mid = Member::fetchIdWithPhone($phone);
            if (!$mid){
                $this->logmsg('empty member of ' . $phone);
                continue;
            }
            $list[$phone] = $this->fetchChildSum($mid, $value[1]);
        }
        echo json_encode($list);
        echo PHP_EOL . '手机号, 100，下及到达100后业绩，下级截止2019-01-16 23:59:59总业绩，下级业绩到达50万后截止2019-01-16 23:59:59总业绩';
        foreach($list as $phone => $data){
            echo PHP_EOL . $phone . ', ' . $data['child_vip_time'] . ', ' . round($data['child_sum_after_vip'] / 2, 2) . ', ' .
                round($data['date_child_sum'] / 2, 2) . ', ' . round(($data['date_child_sum'] - 500000) / 2, 2);
        }
    }

    private function fetchChildSum($mid, $count)
    {
        $date = '2019-01-17 00:00:00';
        $models = WalletInvite::where('pid', $mid)->get()->toArray();
        $this->logmsg('get count ' . count($models));
        $child_vip_time = '未达到';
        $child_sum_after_vip = 0;
        $ids = array_column($models,'member_id');
        // 取下级满足100人时间
        if (count($models) >= $count){
            $child_vip_time = $models[$count-1]['created_at'];
        }
        // 取下级满足100人后下级业绩
        if (count($models) > $count && $models[$count-1]['created_at'] < $date){
            $this->logmsg( ' ids ' . implode(',', $ids));
            $child_sum_after_vip = BtScoreUnlock::whereIn('member_id', $ids)
                ->where('created_at', '<', $date)
                ->where('created_at', '>', $child_vip_time)
                ->sum('amount');
        }
        // 取手下所有要2019-01-06日截止业绩
        $date_child_sum = BtScoreUnlock::whereIn('member_id', $ids)
                ->where('created_at', '<', $date)
                ->sum('amount');
        return compact('child_vip_time', 'child_sum_after_vip', 'date_child_sum');
    }
}
