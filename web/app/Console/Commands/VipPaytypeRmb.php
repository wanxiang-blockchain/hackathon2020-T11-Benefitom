<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Artbc\WalletInvite;
use App\Model\Btshop\BtshopOrder;
use App\Model\Member;
use Illuminate\Console\Command;

class VipPaytypeRmb extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vips:rmbpay';

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
        $member_id = Member::fetchIdWithPhone('15553585267');
//        $member_id = Member::fetchIdWithPhone('15001204748');
        $childs = WalletInvite::where('pid', $member_id)->get()->toArray();
        $ids = array_column($childs, 'member_id');
        $this->logmsg(implode(',',$ids));
        $data = BtshopOrder::leftJoin('btshop_products', 'btshop_products.id', '=', 'btshop_orders.product_id')
            ->whereIn('btshop_orders.member_id', $ids)
            ->where('btshop_orders.stat', 2)
            ->where('btshop_orders.paytype', 2)
            ->select('btshop_orders.amount', 'btshop_orders.id', 'btshop_orders.created_at', 'btshop_products.rmb_price')
            ->get()
            ->toArray();
        $amount = 0;
        foreach ($data as $d){
            $amount += $d['amount'] * $d['rmb_price'];
            if ($amount >= 1000000){
                $this->logmsg($d['created_at']);
            }
        }
        $this->logmsg($amount);
    }
}
