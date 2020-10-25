<?php
namespace App\Service;

use App\Exceptions\TradeException;
use App\Model\Artbc\WalletInvite;
use App\Model\ArtbcLog;
use App\Model\Member;
use App\Repository\MemberRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberService{
    function __construct(MemberRepository $memberRepository) {
        $this->memberRepository = $memberRepository;
    }

    public function create($data) {
        $member = $this->memberRepository
            ->create($data);
        return $member;
    }

    public function deleteForce($id) {
        $this->memberRepository->deleteForce($id);
    }


    public function edit($id,$data){
        return  $member = $this->memberRepository
            ->modify($id,$data);
    }

    public function assets($member_id) {

    }

    public static function isAgent() {
	    try{
		    $member_id = Auth::guard('front')->user()->id;
		    $result = DB::select('select count(1) as aggregate from agents where phone = (select phone from members where id = ?)', [$member_id]);
		    return $result[0]->aggregate > 0;
	    } catch (\Exception $e) {
		    return false;
	    }
    }

	/**
	 * @desc dis
	 *       分级奖励，一级奖励2%， 二级奖励1%
	 */
    public static function dis(Member $member, $amount)
    {
    	$parent = $member->parent;
    	if ($parent){
    		ArtbcLog::add($parent->id, $amount * 0.03, ArtbcLog::TYPE_DIS_1);
    		$pprent = $parent->parent;
    		if ($pprent){
    			ArtbcLog::add($pprent->id, $amount * 0.05, ArtbcLog::TYPE_DIS_2);
		    }
	    }
    }

    public static function walletInvite(Member $member)
    {
        $parent = Member::find($member->wallet_invite_member_id);
        $level = 1;
        while($parent) {
            if ($member->id == $parent->id){
                throw new TradeException('邀请关系产生循环，不可成立。');
            }
            if (WalletInvite::fetchByMidPid($parent->id, $member->id)) {
                throw new TradeException('邀请关系产生循环，不可成立。');
            }
            $walletInvite = WalletInvite::fetchByMidPid($member->id, $parent->id);
            if ($walletInvite){
                if ($walletInvite->level !== $level) {
                    $walletInvite->level = $level;
                    $walletInvite->save();
                }
            }else{
                WalletInvite::add($member->id, $parent->id, $level);
            }
            if ($parent->wallet_invite_member_id > 0) {
                $parent = Member::find($parent->wallet_invite_member_id);
                $level++;
            } else {
                $parent = null;
            }
        }
    }

}