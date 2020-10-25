<?php

namespace App\Http\Controllers\Admin;

use App\Model\Asset;
use App\Model\AssetType;
use App\Model\TradeSet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TradeOrder;
use App\Model\TradeLog;
use App\Service\TradeService;
use App\Model\Member;
class TradeController extends Controller
{
    function __construct(TradeService $tradeService){
        $this->tradeService = $tradeService;
    }

    public function index(Request $request)
	{
        $trade = TradeOrder::leftjoin('members','trade_orders.member_id','=','members.id')
            ->leftjoin('asset_types','trade_orders.asset_type','=','asset_types.code')
	        ->where(function($query)use($request){
		        $phone = request()->get('phone');
		        $asset_type = request()->get('asset_type');
		        $type = request()->get('type');
		        $status = request()->get('status');
		        $beginTime = request()->get('beginTime');
		        $endTime = request()->get('endTime');
		        $beginPrice = request()->get('beginPrice');
		        $endPrice = request()->get('endPrice');
		        $end = date('Y-m-d');
		        if($phone) {
			        $p_id = Member::where('phone', 'like', '%'.$phone.'%')->pluck('id');
			        $query->whereIn('member_id',$p_id);
		        }
		        if(in_array($status, [1,2,3,4])) {
			        $status = $status == 4 ? 0 :$status;
			        $query->where('status', '=', $status);
		        }
		        if($beginTime) {
			        $_endTime = $endTime ? $endTime : $end;
			        $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
			        $query->whereBetween('trade_orders.created_at', [$beginTime, $_endTime]);
		        }
		        if($beginPrice) {
			        $query->where('trade_orders.price', '>=', $beginPrice);
		        }
		        if($endPrice) {
			        $query->where('trade_orders.price', '<=', $endPrice);
		        }
		        if($asset_type) {
			        $query->where('asset_type', $asset_type);
		        }
		        if($type) {
			        $query->where('type', $type);
		        }
	        })->orderBy('trade_orders.id','desc')
            ->select('trade_orders.*','members.phone','asset_types.name')
            ->paginate(10);
        $trade->appends($request->all());
        $assetType = AssetType::get();
        $data = compact("trade",'assetType');

        return view('admin.trade.index',$data);
     }

     function tradeLog(){
        $tradeLog = TradeLog::leftjoin('members as m',function($join){
                        $join->on('trade_logs.buyer_id','=','m.id');
                   })
                   ->leftjoin('members as me','trade_logs.seller_id','=','me.id')
                   ->leftjoin('asset_types','trade_logs.asset_type','=','asset_types.code')
                   ->where(function($query){
                        $buyPhone = request()->get('buyPhone');
                        $sellPhone = request()->get('sellPhone');
                        $beginTime = request()->get('beginTime');
                        $endTime = request()->get('endTime');
	                    $beginPrice = request()->get('beginPrice');
	                    $endPrice = request()->get('endPrice');
                        $end = date('Y-m-d');
                        if($buyPhone) {
                            $b_id = Member::where('phone', 'like', '%'.$buyPhone.'%')->pluck('id');
                            $query->whereIn('buyer_id',$b_id);
                        }
                       if($sellPhone) {
                           $s_id = Member::where('phone', 'like', '%'.$sellPhone.'%')->pluck('id');
                           $query->whereIn('seller_id',$s_id);
                       }
                       if($beginTime) {
                           $_endTime = $endTime ? $endTime : $end;
                           $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
                           $query->whereBetween('trade_logs.created_at', [$beginTime, $_endTime]);
                       }
	                   if($beginPrice) {
		                   $query->where('trade_logs.price', '>=', $beginPrice);
	                   }
	                   if($endPrice) {
		                   $query->where('trade_logs.price', '<=', $beginPrice);
	                   }

                   })
                   ->where('type',1)
                   ->orderBy('trade_logs.id','desc')
                   ->select('trade_logs.*','m.phone as buy_phone','me.phone as sell_phone','asset_types.name')
                   ->Paginate(10);
         $tradeLog->appends(request()->all());
         $assetType = AssetType::get();
        $data = compact("tradeLog","assetType");
        return view('admin.trade.log',$data);
     }

    function revoked($id){
         $status = $this->tradeService->cancelOrder($id);
         if($status != -1){
             return ['code' => 200, 'message' => 'success'];
         }else{
             return ['code' => 201];
         }
    }

    public function set()
    {
        $assetType = AssetType::where('code', '<>', 'T000000001')->get()->toArray();
        $tradeSet = TradeSet::where('asset_type', '<>', 'T000000001')->get()->keyBy(function ($item){
            return strtoupper($item['asset_type']);
        })->toArray();
        return view('admin.trade.set', ['assetType'=>$assetType, 'tradeSet'=>$tradeSet]);
    }

    public function setPost(Request $request)
    {
        $request->flash();
        $this->validate($request, [
            'asset_type'    => 'required',
            'start'    => 'required',
            'end' => 'required|after:'.$request->get('start'),
            'rate'=>['required'],
            'limit'=>'required',
            'trade_start' =>'required',
        ]);
        $tradeSet = TradeSet::firstOrNew(['asset_type'=>$request->get('asset_type')]);
        $tradeSet->asset_type = $request->get('asset_type');
        $tradeSet->start = $request->get('start');
        $tradeSet->end = $request->get('end');
	    $tradeSet->start2 = $request->get('start2');
	    $tradeSet->end2 = $request->get('end2');
        $tradeSet->rate = $request->get('rate');
        $tradeSet->limit = $request->get('limit');
        $tradeSet->trade_start = $request->get('trade_start');
	    $tradeSet->t_plus= $request->get('t_plus');
        $tradeSet->save();
        return redirect()->route('trade/set');
    }

}
