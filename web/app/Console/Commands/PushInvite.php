<?php

namespace App\Console\Commands;

use App\Model\Member;
use App\Utils\DissysPush;
use Illuminate\Console\Command;

class PushInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:invite {phone} {parent}';

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
        $phone = $this->argument('phone');
        $parent = $this->argument('parent');
        if (empty($phone) || empty($parent)){
            return $this->signature;
        }
        $model = Member::fetchModelByPhone($phone);
        $parentModel = Member::fetchModelByPhone($parent);
        if (empty($model) || empty($parentModel)){
            return 'empty model or empty parent';
        }
        if ($model->wallet_invite_member_id !== $parentModel->id){
            return $parent . ' is not ' . $phone . '\'s parent';
        }
//        DissysPush::appendParent($phone, $parent);
    }
}
