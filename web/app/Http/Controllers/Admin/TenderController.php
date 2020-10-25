<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/17
 * Time: 上午10:32
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Member;
use App\Model\Tender\Tender;
use App\Model\Tender\TenderAdminCharge;
use App\Model\Tender\TenderAsset;
use App\Model\Tender\TenderFeedback;
use App\Model\Tender\TenderFlow;
use App\Model\Tender\TenderGuess;
use App\Model\Tender\TenderLog;
use App\Model\Tender\TenderMargin;
use App\Model\Tender\TenderMsg;
use App\Model\Tender\TenderWithdraw;
use App\Service\ValidatorService;
use App\Utils\DateUtil;
use App\Utils\ResUtil;
use App\Utils\TenderConstUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TenderController extends Controller
{
	/**
	 * @var Request
	 * @type Request
	 */
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}


	public function index()
	{
		$query = new Tender();
		$models = $query->where(function($query) {
			$name = $this->request->get('name');
			$type = $this->request->get('type');
			if($name) {
				$query->where('name', 'like', "%{$name}%");
			}
			if($type) {
				$query->where('type', $type);
			}
		})->orderByDesc('id')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.index', compact('models'));
	}

	public function create(ValidatorService $validatorService)
	{
		if($this->request->isMethod('get')){
			return view('admin.tender.create');
		} else if($this->request->isMethod('post')) {
			$data = $this->request->all();
			$rule = [
				'name' => 'required',
				'code' => 'required',
				'type' => 'required|in:0,1',
				'info' => 'required',
				'stat' => 'required|in:-1,0',
				'banner' => 'required',
				'video' => 'required',
				'valuation' => 'required',
			];

			if($data['type'] == Tender::TENDER){

				$rule = array_merge($rule, [
					'guess_start' => 'required|date',
					'guess_end' => 'required|date|after:guess_start',
					'tender_start' => 'required|date|after:guess_end',
					'tender_end' => 'required|date|after:tender_start',
				]);
			} else {
				$rule = array_merge($rule, [
					'starting_price' => 'required|numeric',
					'tender_start' => 'required|date',
					'tender_end' => 'required|date|after:tender_start',
				]);
			}
			$this->validate($this->request, $rule);

			$path = $this->request->file('banner')->store("public/tender", 'public');
			$data["banner"] = $path;

			$path = $this->request->file('poster')->store("public/tender", 'public');
			$data["poster"] = $path;

			$data['guess_count'] = rand(90, 130);

			Tender::create($data);
			return redirect('admin/tender?nav=10|1');
		}
	}

	public function edit()
	{
		if($this->request->isMethod('get')){
			$model = Tender::find($this->request->get('id'));
			return view('admin.tender.edit', compact('model'));
		} else if($this->request->isMethod('post')) {
			$data = $this->request->all();
			$rule = [
				'name' => 'required',
				'code' => 'required',
				'type' => 'required|in:0,1',
				'info' => 'required',
				'stat' => 'required|in:-1,0',
//				'banner' => 'required',
				'video' => 'required',
				'valuation' => 'required',
			];

			if($data['type'] == Tender::TENDER){
				$rule = array_merge($rule, [
					'guess_start' => 'required|date',
					'guess_end' => 'required|date|after:guess_start',
					'tender_start' => 'required|date|after:guess_end',
					'tender_end' => 'required|date|after:tender_start',
				]);
			} else {
				$rule = array_merge($rule, [
					'starting_price' => 'required|numeric',
					'tender_start' => 'required|date',
					'tender_end' => 'required|date|after:tender_start',
				]);
			}
			$this->validate($this->request, $rule);
			$file = $this->request->file('banner');
			if($file){
				$path = $this->request->file('banner')->store("public/tender", 'public');
				$data["banner"] = $path;
			}
			$file = $this->request->file('poster');
			if($file){
				$path = $this->request->file('poster')->store("public/tender", 'public');
				$data["poster"] = $path;
			}
			$model = Tender::find($data['id']);
			if($model) {
				$model->update($data);
			}
			return redirect('admin/tender?nav=10|1');
		}
	}

	public function withdraw()
	{
		$query = new TenderWithdraw();
		$models = $query->where(function($query) {
			$phone = $this->request->get('phone');
			if($phone) {
				$member_id = Member::fetchIdWithPhone($phone);
				$query->where('member_id', $member_id);
			}
			$beginTime = request()->get('beginTime');
			$endTime = request()->get('endTime');
			$end = date('Y-m-d');
			$stat = $this->request->get('stat');
			if($stat){
				$query->where('stat', $stat);
			}

			if($phone){
				$member = Member::where('phone','like','%'.$phone.'%')->pluck('id');
				$query->whereIn('member_id',$member);
			}
			if($beginTime) {
				$_endTime = $endTime ? $endTime : $end;
				$_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
				$query->whereBetween('recharge_audits.created_at', [$beginTime, $_endTime]);
			}
		})->orderByDesc('created_at')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.withdraw', compact('models'));
	}

	public function flow()
	{
		$query = new TenderFlow();
		$models = $query->where(function($query) {
			$phone = $this->request->get('phone');
			$type = $this->request->get('type');
			if($phone) {
				$member_id = Member::fetchIdWithPhone($phone);
				$query->where('member_id', $member_id);
			}
			if($type) {
				$query->where('type', $type);
			}
		})->orderByDesc('created_at')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.flow', compact('models'));
	}

	public function guess()
	{
		$query = new TenderGuess();
		$models = $query->where(function($query) {
			$phone = $this->request->get('phone');
			$winner_type = $this->request->get('winner_type');
			$id = $this->request->get('id');
			if($id){
				$query->where('tender_id', $id);
			}
			if($phone) {
				$member_id = Member::fetchIdWithPhone($phone);
				$query->where('member_id', $member_id);
			}
			if($winner_type) {
				$query->where('winner_type', $winner_type);
			}
		})->orderByDesc('created_at')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.guess', compact('models'));
	}

	public function tender()
	{
		$id = intval($this->request->get('id'));
		if (empty($id)) {
			return redirect('/admin/tender');
		}
		$query = new TenderLog();
		$models = $query->where(function($query) use ($id) {

			$query->where('tender_id', $id);

			$phone = $this->request->get('phone');
			if($phone) {
				$member_id = Member::fetchIdWithPhone($phone);
				$query->where('member_id', $member_id);
			}

		})->orderByDesc('created_at')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.tender', compact('models'));
	}

	public function delete()
	{

	}

	public function winners()
	{
		return '获奖列表';
	}


	// 管理员充小红花
	public function charge(Request $request, ValidatorService $validatorService)
	{
		if($request->method() == 'GET'){
			$query = new TenderAdminCharge();
			$models = $query->where(function($query) {
				$phone = $this->request->get('phone');
				if($phone) {
					$member_id = Member::fetchIdWithPhone($phone);
					$query->where('member_id', $member_id);
				}
			})->orderByDesc('created_at')->paginate(10);
			$models->appends($this->request->all());
			$key  = trim(file_get_contents("../rsa_1024_pub.pem"));
			return view('admin.tender.charge', compact('key', 'models'));
		}else {
			$data = $request->all();
			if (openssl_private_decrypt(base64_decode($data['password']), $decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
				$data['password'] = $decrypted;
			}
			$valite = $validatorService->checkValidator([
				'phone' => 'required|numeric',
				'amount' => 'required|numeric',
				'password' => 'required',
				'type' => 'required|in:0,1'
			], $data);
			if ($valite['code'] != 200) {
				return $valite;
			}

			if (!Hash::check($data['password'], Auth()->user()->password)) {
				return ResUtil::error(201, '密码错误');
			}

			$member = Member::where('phone', $data['phone'])->first();

			if(empty($member)) {
				return ResUtil::error(201, '用户不存在');
			}

			TenderAdminCharge::create([
				'add_admin' => Auth()->id(),
				'member_id' => $member->id,
				'amount' => $data['amount'],
				'type' => $data['type']
			]);

			return ResUtil::ok();

		}
	}

	public function chargeReject(Request $request)
	{
		$id = $request->get('id');
		$note = $request->get('note');
		DB::beginTransaction();
		try{
			$model = TenderAdminCharge::find($id);
			if(empty($model) || $model->stat > 0) {
				throw new \Exception('数据不存在或已处理');
			}

			$model->note = $note;
			$model->stat = TenderAdminCharge::STAT_REJECT;
			if(!$model->save()){
				throw new \Exception('服务器异常，请稍后重试');
			}
			DB::commit();
			return ResUtil::ok();

		}catch (\Exception $e) {
			DB::rollBack();
			return ResUtil::error(201, $e->getMessage());
		}
	}

	public function chargeAccept($id, Request $request)
	{
//		$id = $request->get('id');
		DB::beginTransaction();
		try{
			$model = TenderAdminCharge::find($id);
			if(empty($model) || $model->stat > 0) {
				throw new \Exception('数据不存在或已处理');
			}

			$model->stat = TenderAdminCharge::STAT_ACCEPT;
			if(!$model->save()){
				throw new \Exception('服务器异常，请稍后重试');
			}
			TenderAsset::add($model->member_id, $model->amount, $model->type == 0 ? TenderFlow::TYPE_ADMIN_RECHARGE : TenderFlow::TYPE_ADMIN_REG_GIFT);
			if($model->type == 1){
				TenderMsg::setTempInviteReg($model->member_id, $model->amount);
			}
			DB::commit();
			return ResUtil::ok();

		}catch (\Exception $e) {
			DB::rollBack();
			return ResUtil::error(201, $e->getMessage());
		}
	}


	// 提现审核
	public function adopt()
	{
		$id = $this->request->get('id');
		$withdraw = TenderWithdraw::find($id);
		if(!$withdraw) {
			return ['code'=>201, 'message'=>'fail'];
		}
		if(!in_array(\Auth::guard('web')->user()->role_type, [1, 3])) {
			return ['code'=>201, 'message'=>'无权限操作'];
		}
		if($withdraw['stat'] == TenderWithdraw::STAT_INIT){
			$withdraw->stat = TenderWithdraw::STAT_ACCEPT;
			$withdraw->save();
			return ['code'=>200, 'message'=>'success'];
		}else{
			return ['code'=>201, 'message'=>'请勿重复提交'];
		}
	}

	// 提现拒绝
	public function reject()
	{
		$id = $this->request->get('id');
		if(!in_array(\Auth::guard('web')->user()->role_type, [1, 3])) {
			return ['code'=>201, 'message'=>'无权限操作'];
		}
		$reason = $this->request->get('note');
		$withdraw = TenderWithdraw::where('id',$id)->first();
		if($withdraw['stat'] == TenderWithdraw::STAT_INIT){
			try{
				DB::beginTransaction();
				$withdraw->note = $reason;
				$withdraw->stat = TenderWithdraw::STAT_REJECT;
				$withdraw->save();
				TenderAsset::add($withdraw->member_id, $withdraw->amount * TenderConstUtil::PARITY, TenderFlow::TYPE_WITHDRAW_REJECT);
				TenderMsg::setTempWithdarwReject($withdraw->member_id, $withdraw->created_at->format('Y-m-d H:i:s'), $withdraw->amount, $withdraw->note);
				DB::commit();
				return ['code'=>200];
			}catch (\Exception $e){
				DB::rollBack();
				return ['code'=>204,'message'=>$e->getMessage()];
			}
		}else {
			return ['code' => 201, 'message' => '请不要重复提交'];
		}
	}

	/**
	 * 结束拍品拍卖，修改stat=3，结束之后用户可退还保证金
	 * @desc finish
	 */
	public function finish($id)
	{
		DB::beginTransaction();
		try{
			$model = Tender::find($id);
			if (empty($model)) {
				throw new \Exception('请求异常');
			}

			if ($model->stat == Tender::STAT_DONE) {
				throw new \Exception('拍品已结束');
			}

			if ($model->isDark()) {
				if ($model->stat != Tender::STAT_GUESS_COUNT_FINISHED) {
					throw new \Exception('拍品尚不可结束');
				}
			}

			if($model->tender_end > DateUtil::now()) {
				throw new \Exception('拍品还在拍卖期');
			}

			$model->stat = Tender::STAT_DONE;
			if (!$model->save()){
				throw new \Exception('服务器异常，请稍后再试！');
			}
			DB::commit();
			return ['code' => 200, 'message' => '成功'];

		}catch (\Exception $e) {
			DB::rollBack();
			Log::error($e->getTraceAsString());
			return ['code' => 201, 'message' => $e->getMessage()];
		}
	}

	public function margin()
	{
		$query = new TenderMargin();
		$models = $query->where(function($query) {
			$phone = $this->request->get('phone');
			if($phone) {
				$member_id = Member::fetchIdWithPhone($phone);
				$query->where('member_id', $member_id);
			}
		})->orderByDesc('created_at')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.margin', compact( 'models'));
	}

	public function marginDeduct($id)
	{
		DB::beginTransaction();
		try{
			$model = TenderMargin::find($id);
			if(!$model){
				throw new \Exception('请求异常');
			}
			if(!$model->delete()){
				throw new \Exception('服务器异常，请稍后再试');
			}
			DB::commit();
			return ResUtil::ok();
		}catch (\Exception $e) {
			DB::rollBack();
			return ['code' => 201, 'data' => $e->getMessage()];
		}
	}

	public function feedback()
	{
		$query = new TenderFeedback();
		$models = $query->where(function($query) {
			$phone = $this->request->get('phone');
			if($phone) {
				$member_id = Member::fetchIdWithPhone($phone);
				$query->where('member_id', $member_id);
			}
		})->orderByDesc('id')->paginate(10);
		$models->appends($this->request->all());
		return view('admin.tender.feedback', compact('models'));
	}

}