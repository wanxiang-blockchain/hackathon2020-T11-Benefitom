<?php

namespace App\Repository;


use App\Model\AlipayLogs;

class AlipayLogsRepository
{
    public function export($request)
    {
        $fee = AlipayLogs::where('status', '=', 1);
        $end = date('Y-m-d');
        if ($request->get('phone')) {
            $fee->where('members.phone', 'like', '%' . $request->get('phone') . '%');
        }
        $beginTime = $request->get('beginTime', '');
        $endTime = $request->get('endTime', '');
        if ($beginTime) {
            $_endTime = $endTime ? $endTime : $end;
            $_endTime = date('Y-m-d', strtotime('+1 days', strtotime($_endTime)));
            $fee->whereBetween('alipay_logs.created_at', [$beginTime, $_endTime]);
        }
        $fee->leftJoin('members', 'alipay_logs.member_id', '=', 'members.id');
        $fee->orderBy('alipay_logs.id','desc');
        $c = $fee->select('alipay_logs.id', 'members.phone', 'alipay_logs.money', 'alipay_logs.poundage','alipay_logs.type', 'alipay_logs.created_at');
        $_fee = $c->get()->toArray();
        $_fee = array_map(function(&$item){
            $item['type'] = $item['type'] == 1 ? '支付宝' : '微信';
            return $item;
        }, $_fee);
        return $_fee;
    }

}