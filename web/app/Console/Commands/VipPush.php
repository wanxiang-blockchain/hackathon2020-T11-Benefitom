<?php

namespace App\Console\Commands;

use App\Model\Cms\Push;
use App\Model\Vip;
use App\Utils\DateUtil;
use App\Utils\PushUtil;
use Illuminate\Console\Command;

class VipPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vip:push';

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
        //
        return 404;
        $vips = Vip::all();
        foreach ($vips as $vip){
            $this->push($vip->phone);
        }
    }

    public function push($phone)
    {
        $con = '尊敬的用户 领导人您好，由于近期支付宝提额限制问题，为更好的满足我们新增会员的体验度，公司临时为领导人开通银行卡专属提现通道，单日限额5000元（24小时内到账），技术部门正在积极解决相关问题，感谢大家鼎力支持！' . "\n\n";
        $push = new \App\Model\Cms\Push();
        $push->fill([
            'type' => Push::TYPE_NOTICE,
            'con_id' => 0,
            'con' => $con,
            'title' => '重要通知：关于领导人专属提现通道相关通知！',
            'subtitle' => $con,
            'push_to' => $phone,
            'push_at' => DateUtil::now(),
            'stat' => 1
        ]);
        $push->save();
        PushUtil::push([$phone], $push);
    }
}
