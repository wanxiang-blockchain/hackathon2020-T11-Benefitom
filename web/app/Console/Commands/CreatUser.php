<?php

namespace App\Console\Commands;

use App\Model\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreatUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a manage user';

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
	    User::create(
		    [
			    "phone"     => "13800138000",
			    "password"  => Hash::make("agangdi"),
			    "name"      => 'admin',
			    "role_type" => 1
		    ]
	    );
    }
}
