<?php
namespace App\Repository;

use App\Model\Member;
use App\Model\Account;
class MemberRepository{

    public function create($data) {
        $data['password'] = \Hash::make($data['password']);
        $data['is_lock']  = 0;
        $member =  Member::create($data);
        $account = new Account();
        $account -> member_id = $member['id'];
        $account -> trade_pwd = \Hash::make($data['trade_pwd']);
        $account -> is_lock = 0;
        $account->save();
        return $member;
    }
    
    public function modify($id, $data) {
        if(isset($data['password'])){
            $data['password'] = \Hash::make($data['password']);
        }
        $member = Member::where("id", $id)
            ->update($data);
        return $member;
    }

    public function deleteForce($id) {
        $member = Member::find($id);
        $member->account()->delete();
        $member->delete();
    }

    public function assets($member_id) {
        return Member::find($member_id)
            ->assets;
    }

    public function addAsset($member_id) {
        
    }
}