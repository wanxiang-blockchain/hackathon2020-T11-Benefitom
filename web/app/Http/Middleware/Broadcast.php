<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/26
 * Time: 上午10:35
 */

namespace App\Http\Middleware;
use App\Model\Member;
use App\Model\Tender\TenderBroadcast;
use App\Model\Tender\TenderBroadcastRead;
use Closure;


class Broadcast
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
//		$member = Member::current();
//		$broadcast_read =TenderBroadcastRead::where('member_id', $member->id)
//			->where('broad_id', 1)->first();
//		if(empty($broadcast_read)) {
//			return redirect('/tender/contract');
//		}
		return $next($request);
	}
}