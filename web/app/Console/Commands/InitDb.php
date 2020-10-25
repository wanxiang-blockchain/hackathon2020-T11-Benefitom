<?php

namespace App\Console\Commands;

use App\Model\Category;
use App\Model\Finance;
use App\Model\Picture;
use Illuminate\Console\Command;
use App\Model\User;
use App\Model\Role;
use App\Model\AssetType;
use App\Model\FinanceType;
use Hash;
class InitDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add transfer project init data';

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
        $this->createUser();
        $this->createRole();
        $this->createAssetType();
        $this->createFinanceType();
    }

    function createUser() {
        User::truncate();
        User::create(
            [
                "phone"     => "13800138000",
                "password"  => Hash::make("aaaaaa"),
                "name"      => 'admin',
                "role_type" => 1
            ]
        );
    }

    function createRole() {
        Role::truncate();

        Role::create([
            "type"   => 1,
            "name"   => '管理员'
        ]);

        Role::create([
            "type"   => 2,
            "name"   => "业务员"
        ]);

        Role::create([
            "type"  => 3,
            "name"  => "财务"
        ]);
        Role::create([
            "type"  => 4,
            "name"  => "财务二级"
        ]);
    }

    public function createAssetType()
    {
        AssetType::truncate();
        AssetType::create(["code" => "T000000001", "name" => '现金', '用来表示用户的余额']);
    }

    public function createFinanceType()
    {
        FinanceType::truncate();
        FinanceType::create(["code" => "1", "name" => '管理员充值']);
        FinanceType::create(["code" => "2", "name" => '充值']);
        FinanceType::create(["code" => "3", "name" => '认购']);
        FinanceType::create(["code" => "4", "name" => '提现']);
        FinanceType::create(["code" => "5", "name" => '交易手续费']);
        FinanceType::create(["code" => "6", "name" => '交易']);
    }
}
