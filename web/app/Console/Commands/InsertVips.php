<?php

namespace App\Console\Commands;

use App\Console\Traits\LogTrait;
use App\Model\Vip;
use App\Model\Member;
use Illuminate\Console\Command;

class InsertVips extends Command
{
    use LogTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:vips {file}';

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
        $file = $this->argument('file');
        $phones = file($file);
        foreach ($phones as $phone){
            $phone = trim($phone);
            if (!Member::where('phone', $phone)->exists()) {
                $this->logmsg($phone . ' does not exists');
                continue;
            }
            Vip::insertIfNotExists($phone);
        }
    }
}
