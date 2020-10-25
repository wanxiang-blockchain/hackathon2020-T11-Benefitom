<?php

namespace App\Console\Commands;

use App\Model\AlipayLogs;
use App\Model\Artbc;
use App\Model\ArtbcLog;
use App\Model\Btshop\BlockRechargeLog;
use Illuminate\Console\Command;
use PHPMailer\PHPMailer\PHPMailer;

class EmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

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
    	if (date('H') > '0' && date('H') < '6'){
    		return false;
	    }
        //
	    require_once base_path() . '/vendor/autoload.php';

	    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	    try {
		    //Server settings
		    $mail->SMTPDebug = 1;                                 // Enable verbose debug output
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    $mail->CharSet='utf8';
		    $mail->Host = 'smtp.163.com';  // Specify main and backup SMTP servers
		    $mail->SMTPAuth = true;                               // Enable SMTP authentication
		    $mail->Username = 'deyigongpan@163.com';                 // SMTP username
		    $mail->Password = '04302211yi';                           // SMTP password
		    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = 25;                                    // TCP port to connect to

		    //Recipients
		    $mail->setFrom('deyigongpan@163.com', 'Mailer');
		    $mail->addAddress('553442317@qq.com', 'just like before');     // Add a recipient

		    //Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = '时报';
		    $mail->Body    = $this->body();
		    $mail->AltBody = $this->altBody();

		    $mail->send();
		    echo 'Message has been sent' . PHP_EOL;
	    } catch (\Exception $e) {
		    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	    }
    }

    public function altBody()
    {
    	return date('Y-m-d H', time() - 3600) . '-' . date('Y-m-d H') . '时报';
    }

    public function body()
    {
	    $body = $this->altBody() . PHP_EOL;
    	// 获取充值金额
	    $alipay_logs = $this->fetchCharge();
	    $payCount = count($alipay_logs);
	    $payAmount = 0;
	    foreach ($alipay_logs as $pay){
	        $payAmount += $pay->money;
	    }
	    $body .= "<p>{$payCount}人充值，金额{$payAmount}</p>";

	    // 获取提取数据
	    $tibis = $this->fetchTibi();
	    $tibiCount = count($tibis);
	    $tibiAmount = 0;
	    foreach($tibis as $t){
	        $tibiAmount += $t->amount;
	    }

	    $body .= "<p>{$tibiCount}人提取{$tibiAmount}个</p>";

	    // 获取充值数据
        $recharges = $this->fetchCharge();
        $rechargeCount = count($recharges);
        $rechargeAmount = 0;
        foreach ($recharges as $recharge) {
            $rechargeAmount += $recharge->amount;
        }

        $body .= "<p>{$rechargeCount}人充币{$rechargeAmount}个</p>";

	    if ($payCount == 0 && $tibiCount == 0 && $rechargeAmount == 0) {
	    	echo "都是0，不发时报\n";
	    	exit;
	    }

	    return $body;
    }

	/**
	 * 获取充值金额
	 * @desc fetchCharge
	 * @return AlipayLogs
	 */
    public function fetchCharge()
    {
    	$lastHour = date('Y-m-d H:00:00', time() - 3600);
    	return AlipayLogs::where('paid_at', '>', $lastHour)->where('status', 1)->get();
    }

    public function fetchTibi()
    {
    	return ArtbcLog::where('type', ArtbcLog::TYPE_TIBI)->where('stat', 0)->get();
    }

    public function blockRecharge()
    {
        $lastHour = date('Y-m-d H:00:00', time() - 3600);
        return BlockRechargeLog::where('created_at', '>', $lastHour)
            ->where('stat', 2)
            ->get();
    }
}
