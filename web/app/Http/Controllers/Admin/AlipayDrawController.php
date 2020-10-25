<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-22
 * Time: 14:21
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\Btshop\AlipayDraw;
use App\Model\Member;
use Illuminate\Http\Request;

class AlipayDrawController extends Controller
{

    public function index(Request $request)
    {
        $models = AlipayDraw::where(function ($query) use ($request){
            $phone = $request->get('phone');
            $stat = $request->get('stat');
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
            if($stat){
                $query->where('stat','=',$stat);
            }

        })->orderBy('created_at', 'desc')->paginate(10);
        $models->appends(Request()->all());
        return view('admin.wallet.alipay_draw', compact('models'));
    }

}