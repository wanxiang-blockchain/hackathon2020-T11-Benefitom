<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/2/25
 * Time: 下午8:17
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Account;
use App\Model\Finance;
use App\Model\Member;
use App\Model\OpeLog;
use App\Service\AccountService;
use App\Service\FinanceService;
use App\Utils\ResUtil;
use App\Model\WithdrawAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WithdarwAuditController extends Controller
{
	protected $accountService;
	function __construct(AccountService $accountService)
	{
		$this->accountService = $accountService;
	}

	public function index(Request $request)
	{
		$models = WithdrawAudit::where(function($query)use($request) {
			$phone = $request->get('phone');
			$beginTime = request()->get('beginTime');
			$endTime = request()->get('endTime');
			$end = date('Y-m-d');
			if($phone){
				$member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
				$query->whereIn('member_id',$member);
			}
			if($beginTime) {
				$_endTime = $endTime ? $endTime : $end;
				$_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
				$query->whereBetween('created_at', [$beginTime, $_endTime]);
			}

		})->orderBy('id','desc')->paginate(10);
		return view('admin.withdraw_audits.index', compact('models'));
	}

	public function audit(Request $request)
	{
		$id =  $request->get('id');
		$status = $request->get('status');
		$reason = $request->get('reason');
		if (!in_array($status, [1, 2])){
			return ResUtil::error('参数错误');
		}
		DB::beginTransaction();
		try{
			$model = WithdrawAudit::find($id);
			if (empty($model)) {
				return ResUtil::error(201, '数据不存在');
			}
			if ($model->status !== 0){
				throw new \Exception('该提现已审核');
			}
			$model->status = $status;
			$model->reason = $reason;
			$model->audit_id = Auth()->id();
			if (!$model->save()) {
				throw new \Exception('服务器异常，请联系管理员');
			}
			if ($status == 2){
				// 如果是驳回
				$this->accountService->addAsset($model->account->id, Account::BALANCE, $model->amount, '');

				$r = FinanceService::record($model->member->id, Account::BALANCE,
					Finance::ADMIN_WITH_DRAW_REJECT, $model->amount, 0,
					'管理员提现驳回，金额:' . $model->amount . '元');
				if (!$r) {
					throw new \Exception('提现失败');
				}
				OpeLog::record('管理员后台提现驳回, 金额:' . $model->amount . '元', $model->toArray(), $id);
			}
			DB::commit();
			return ResUtil::ok();
		}catch (\Exception $e){
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return ResUtil::error(201, $e->getMessage());
		}
	}

	public function add(Request $request)
	{
		if ($request->isMethod('post')) {

			$pwd = $request->get('password');
			if (openssl_private_decrypt(base64_decode($pwd), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
				$pwd = $decrypted;
			}
			if (!Hash::check($pwd, Auth()->user()->password)) {
				return ['code' => 202, 'data' => '管理员密码不正确'];
			}
			$accountService = new AccountService();
			$phone = $request->get('phone');
			if (empty($phone)) {
				return ['code' => 201, 'data' => '手机号不得为空'];
			}
			$member = Member::fetchModelByPhone($phone);

			if (empty($member)) {
				return ['code' => 201, 'data' => '用户不存在'];
			}
			$account = $member->account;
			$amount = intval($request->get('amount'));
			if (empty($amount) || !is_numeric($amount) || $amount < 0) {
				return ['code' => 201, 'data' => '请输入正确提现金额'];
			}
			DB::beginTransaction();
			try {
				$balance = $accountService->balance($member->id);
				if ($balance < $amount) {
					throw new \Exception('可用现金不足');
				}
				$ret = WithdrawAudit::create(['member_id' => $member->id, 'amount' => $amount, 'status' => 0]);
				if (!$ret) {
					throw new \Exception('提现失败');
				}
				$accountService->addAsset($account->id, Account::BALANCE, '-' . $amount, '');

				$r = FinanceService::record($member->id, Account::BALANCE, Finance::ADMIN_WITH_DRAW, '-' . $amount, 0, '管理员后台提现，金额:' . $amount . '元');
				if (!$r) {
					throw new \Exception('提现失败');
				}
				OpeLog::record('管理员后台提现, 金额:' . $amount . '元', ['member_id' => $member->id, 'amount' => $amount, 'status' => 0], $phone);
				DB::commit();

				return ['code' => 200, 'data' => '提现申请提交成功，请工作人员于2个工作日内内审核打款'];
			} catch (\Exception $e) {
				\Log::error($_REQUEST);
				\Log::error($e->getTraceAsString());
				DB::rollBack();

				return ['code' => 202, 'data' => $e->getMessage()];
			}
		} else {
			$key = trim(file_get_contents("../rsa_1024_pub.pem"));
			return view('admin.withdraw_audits.add', compact('key'));
		}
	}
}