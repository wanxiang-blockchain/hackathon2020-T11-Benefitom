<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Btshop\SuperMember;
use App\Model\Member;
use Illuminate\Console\Command;

class AddSuperMember extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:super {phone}';

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
        $phone = $this->argument('phone');
        if (SuperMember::isSuper($phone)){
             return $this->logmsg($phone . ' is super');
        }
        if (!Member::fetchModelByPhone($phone)){
            return $this->logmsg($phone . ' is not exist');
        }
        SuperMember::create([
            'phone' => $phone
        ]);
    }
}
