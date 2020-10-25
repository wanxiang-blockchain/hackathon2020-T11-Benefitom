<?php

namespace App\Console\Commands;

use App\Model\Artbc\Eth;
use App\Model\Artbc\Invite;
use App\Model\Artbc\Msg;
use App\Utils\DateUtil;
use Illuminate\Console\Command;

class TelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:bot';

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

	private function storeMsg($msg){
		$from_user_name =  $msg['from']['first_name'] . ' ' . $msg['from']['last_name'];
		Msg::create([
			'from_user' => $msg['from']['id'],
			'to_user' => $msg['chat']['id'],
			'con' => json_encode($msg),
			'from_user_name' => $from_user_name,
			'to_user_name' => $msg['chat']['title'],
			'msg_id' => $msg['message_id'],
			'created_at' => DateUtil::now()
		]);
	}

	private function storeInvites($msg, $members){
		if (!is_array($msg) && !is_array($members)) {
			return false;
		}
		foreach ($members as $member){
			$invite_user_name = $msg['from']['first_name'] . ' ' . $msg['from']['last_name'];
			$to_user_name = $member['first_name'] . ' ' . $member['last_name'];
			Invite::create([
				'invite_user' => $msg['from']['id'],
				'to_user' => $member['id'],
				'invite_user_name' => $invite_user_name,
				'to_user_name' => $to_user_name,
				'msg_id' => $msg['message_id'],
				'created_at' => DateUtil::now()
			]);
		}
	}

	private function storeEth($msg, $addr){
		$nickname = $msg['from']['first_name'] . ' ' . $msg['from']['last_name'];
		Eth::create([
			'addr' => $addr,
			'user' => $msg['from']['id'],
			'nickname' => $nickname,
			'msg_id' => $msg['message_id'],
			'created_at' => DateUtil::now()
		]);
	}

	private function leftInvite($member)
	{
		Invite::where('to_user', $member['id'])->delete();
	}

	/**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    //
	    include(base_path() . '/vendor/autoload.php');
	    $telegram = new \Telegram("558540733:AAE0bwgbNFDxCAOxnGjYLzSufX_-LV2NyIc");
	    $config = [
	        'chat_id' => ['-256380268']
	    ];
	    while (true) {
		    try {
			    $req = $telegram->getUpdates();
			    file_put_contents('/tmp/xiao.log', json_encode($req) . "\n", FILE_APPEND);
			    for ($i = 0; $i < $telegram->UpdateCount(); $i++) {
				    // You NEED to call serveUpdate before accessing the values of message in Telegram Class
				    $telegram->serveUpdate($i);
				    $text = trim($telegram->Text());
				    file_put_contents('/tmp/xiao.log', "text: $text" . "\n", FILE_APPEND);
				    $chat_id = $telegram->ChatID();
				    //				    $from_chat_id = $telegram->FromChatID();
				    $msg = $req['result'][$i]['message'];

				    $chat = $telegram->getData();

				    if ($text == '/start') {
					    $reply = 'Working';
					    $content = ['chat_id' => $chat_id, 'text' => $reply];
					    $telegram->sendMessage($content);
					    continue;
				    }

				    if ($msg['chat']['type'] !== 'group'){
				    	continue;
				    }

				    $this->storeMsg($msg);

                    if (!in_array($chat_id, $config['chat_id'])) {
	                    $content = ['chat_id' => $chat_id, 'text' => '该群不是ArTBC运营官方群，无法识别您的命令，请加入 https://0.plus/joinchat/HoBi1A9IDWwFKKcaMrRlsg'];
	                    $telegram->sendMessage($content);
                        continue;
                    }

				    if (isset($msg['new_chat_member'])) {
					    // 如果是添加新人
					    $this->storeInvites($msg, $msg['new_chat_members']);
				    }

				    if (strpos($text, '/addr') === 0) {
					    $arr = explode('/addr', $text);
					    $addr = trim($arr[1]);
//					    $addr = str_replace('@iic机器人', '', $addr);
					    $this->storeEth($msg, $addr);
					    $nickname = $msg['from']['first_name'] . $msg['from']['last_name'];
//					    $reply = '@' . $nickname . ' 您的地址已收到，注册https://yigongpan.com/register 即可获赠100artbc';
					    $reply = '@' . $nickname . ' 您的地址已收到，欢迎注册https://yigongpan.com/register 领取ArTBC';
					    $content = ['chat_id' => $chat_id, 'text' => $reply];
					    $telegram->sendMessage($content);
				    }

				    if (isset($msg['left_chat_member'])) {
					    // 删除成员，如果删除成员，同时删除其被邀请记录
					    $this->leftInvite($msg['left_chat_member']);
				    }
			    }
			    sleep(1);
		    } catch (\Exception $e) {
			    echo $e->getTraceAsString();
		    }
	    }
    }
}
