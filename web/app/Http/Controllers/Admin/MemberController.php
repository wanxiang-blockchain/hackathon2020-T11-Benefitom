<?php

namespace App\Http\Controllers\Admin;

use App\Model\OpeLog;
use App\Service\ValidatorService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Service\MemberService;
use App\Model\Member;
use App\Model\Account;
use App\Model\Finance;
use Illuminate\Http\Request;
use App\Service\AccountService;
class MemberController extends Controller
{
    function __construct(MemberService $memberService,AccountService $accountService) {
        $this->memberService = $memberService;
        $this->accountService = $accountService;
    }

    function index(Request $request){
        $Member = new Member();
        $member = $Member->where(function($query)use($request){
            $name = request()->get('name');
            $is_lock = request()->get('is_lock');
            $beginTime = request()->get('beginTime');
            $endTime = request()->get('endTime');
            $end = date('Y-m-d');
            if($name) {
                $query->where('phone', 'like', '%'.$name.'%');
            }
            if(in_array($is_lock, [1,2])) {
                $lock = $is_lock == 1 ? 1 : 0;
                $query->where('is_lock', '=', $lock);
            }
            if($beginTime) {
                $_endTime = $endTime ? $endTime : $end;
                $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                $query->whereBetween('created_at', [$beginTime, $_endTime]);
            }

        })->orderBy('id','desc')->paginate(10);
        $member->appends($request->all());
        $role_type = \Auth::guard('web')->user()->role_type;
        return view('admin.member.index',["member" => $member,"role_type" => $role_type]);
    }

    function getCreate(){
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view('admin.member.create', compact('key'));
    }
    
    function  create(Request $request, ValidatorService $validatorService){
        $data = $request->all();
        if (openssl_private_decrypt(base64_decode(trim($data['password'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die('');
        }
        if (openssl_private_decrypt(base64_decode(trim($data['trade_pwd'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['trade_pwd'] = $decrypted;
        } else {
            die('');
        }
        if (openssl_private_decrypt(base64_decode(trim($data['code'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['code'] = $decrypted;
        } else {
            die('');
        }
        $validator = $validatorService->checkValidator([
//            'name'        => 'between:2,4',
//            'nickname'    => 'unique:members|between:2,10',
            'phone'       => 'required|unique:members|digits:11|numeric',
            'password'    => 'required|between:3,16',
            'trade_pwd'    => 'required|between:6,16|different:password',
//            'code'        => 'between:15,18',
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
        $this->memberService->create($data);
        return ['code'=>200, 'data'=>'成功'];
    }

    function getEdit(Request $request){
        $id = $request->get('id');
        $member = Member::find($id);
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view('admin.member.edit',['member'=>$member, 'key'=>$key]);
    }

    function postEdit(Request $request, ValidatorService $validatorService){
        $id   = $request->input('id');
        $data = $request->all();
        if (openssl_private_decrypt(base64_decode(trim($data['code'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['code'] = $decrypted;
        } else {
            die('');
        }
        if (openssl_private_decrypt(base64_decode(trim($data['password'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die('');
        }
        if(empty($data['password'])){
            unset($data['password']);
        }
        unset($data['_token'], $data['uri'], $data['method'], $data['id'], $data['ip']);
        $validator = $validatorService->checkValidator([
//            'name'        => 'between:2,4,',
//            'nickname'    => 'between:2,10|unique:members,nickname,'.$id,
            'phone'       => 'required|digits:11|numeric|unique:members,phone,'.$id,
            'password'    => 'sometimes|between:3,16',
//            'code'        => 'between:15,18',
        ], $data);

        if($validator['code'] != 200) {
            return $validator;
        }

	    $oldPhone = Member::fetchPhoneWithId($id);

        DB::beginTransaction();
	    try{
		    if ($this->memberService->edit($id,$data)) {
			    // 修改passport
			    DB::connection('passport')->statement('update sdt_user set phone = ?, update_time = ? where phone = ?', [$data['phone'], date('Y-m-d H:i:s'), $oldPhone]);
			    DB::connection('passport')->statement('update user set phone = ?, update_time = ?, old_phone = ? where phone = ?', [$data['phone'], date('Y-m-d H:i:s'),$oldPhone, $oldPhone]);
			    OpeLog::record('修改手机号' . $oldPhone . '为' . $data['phone'], ['phone' => $data['phone'], 'old_phone' => $oldPhone], $oldPhone);
			    DB::commit();
		    } else {
		    	throw new \Exception('修改手机号失败');
		    }
        }catch (\Exception $e) {
            DB::rollBack();
		    return ['code'=>201, 'data'=>$e->getMessage()];
        }
        return ['code'=>200, 'data'=>'成功'];

    }
    function change(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        $member = Member::find($id);
        if(!$member) {
            return ['code'=>201, 'message'=>'fail'];
        }
        $member->is_lock = $status;
        $member->save();
        return ['code'=>200, 'message'=>'success'];
    }

    function detail($member_id){
        $data = [
            'has' => $this->accountService->notBalanceAmount($member_id),
            'balance' => $this->accountService->balance($member_id),
            'finance' => Finance::where('member_id',$member_id)->orderBy('id','desc')->paginate(10),
        ];
        return view('admin/member/detail',$data);
    }

}