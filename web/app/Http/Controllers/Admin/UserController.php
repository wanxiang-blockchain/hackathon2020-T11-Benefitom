<?php

namespace App\Http\Controllers\Admin;

use App\Model\User;
use App\Service\ValidatorService;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\UserService;
use App\Model\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = User::where(function($query){
            $phone = request()->get('phone');
            $type = request()->get('type');
            if($phone) {
                $query->where('phone', 'like', '%'.$phone.'%');
            }
            if($type) {
                $query->where('role_type', '=', $type);
            }
        })->orderBy('created_at','desc')->paginate(10);
        $users->appends(Request()->all());
        return view('admin.user.index', ['user'=>$users]);
    }

    public function getCreate(){
        $roles = Role::all()->map(function($v) {
            return [
                "name"  => $v["name"],
                "value" => $v['type']
            ];
        });
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view("admin.user.create", ["values" => $roles, 'key'=>$key]);
    }


    function create(Request $request, ValidatorService $validatorService) {
        $data = $request->all();
        if (openssl_private_decrypt(base64_decode(trim($data['password'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die('');
        }
        $validator = $validatorService->checkValidator([
            'name'      => 'required',
            'password'  => 'required',
            'role_type' => 'digits:1',
            'phone'     => 'unique:users|digits:11'
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
        $this->userService->create($data);
        return ['code'=>200, 'data'=>'成功'];
    }

    function  delete (Request $request){
        $this->userService->delete($request->input('id'));
        return ['code'=>200];
    }


    function getEdit(Request $request) {
        $id = $request->input('id');
        if(\Auth::id()!=1 && $id!=\Auth::id()){
            echo "<script>alert('没有权限');window.history.go(-1);</script>";die;
        }
        $user = $this->userService->getUser($request->input("id"));
        $user = $user->toarray();
        $roles = Role::all()->map(function($v) {
            return [
                "name"  => $v["name"],
                "value" => $v['type']
            ];
        });
        $key  = trim(file_get_contents("../rsa_1024_pub.pem"));
        return view("admin.user.edit", [
            "user" => $user,
            "values" => $roles,
            'key'=>$key
        ]);
    }


    function postEdit(Request $request, ValidatorService $validatorService) {
        $id   = $request->input('id');
        $data = $request->all();
        if (openssl_private_decrypt(base64_decode(trim($data['password'])),$decrypted, trim(file_get_contents("../rsa_1024_priv.pem")))) {
            $data['password'] = $decrypted;
        } else {
            die('');
        }
        $validator = $validatorService->checkValidator([
            'name'      => 'required|max:255',
            'phone'     => 'digits:11|unique:users,phone,'.$id,
        ], $data);
        if($validator['code'] != 200) {
            return $validator;
        }
        unset($data['_token'], $data['uri'], $data['_url'], $data['method'], $data['id'], $data['ip']);
        if(!isset($data['password']) || $data['password'] == null || !$data['password']) {
            unset($data['password']);
        } else {
            $data['password'] = \Hash::make($data['password']);
        }
        $user_id = Auth::guard('web')->user()->id;
        $this->userService->edit($id,$data);
        if(isset($data['password']) && $data['password'] && $id == $user_id) {
            $auth = new AuthController();
            $auth->logout();
        }
        return ['code'=>200, 'data'=>'成功'];
    }
}