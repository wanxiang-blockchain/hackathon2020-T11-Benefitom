
@extends('layouts.admin')

@section('title', '充值列表')

@section('content')
    <div class="page-title">
        <h2>充值列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('finance/alilog')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">账号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入用户的手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">时间</label>
                        <div class="col-xs-12 col-sm-8 col-lg-2 ">
                            <input type="text" class="form-control datepicker" name="beginTime" value="{{request()->get('beginTime')}}" placeholder="开始时间">
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-2 ">
                            <input type="text" class="form-control datepicker" name="endTime" value="{{request()->get('endTime')}}" placeholder="结束时间">
                        </div>
                        <div class="col-xs-12 col-sm-2 col-lg-2">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>充值时间</th>
                    <th>公盘账号</th>
                    <th>支付宝账号</th>
                    <th>充值金额</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['gmt_create']}}</td>
                        <td>{{$value['phone']}}</td>
                        <td>{{$value['buyer_logon_id']}}</td>
                        <td>{{$value['money']}}</td>
                        <td>{{$value['status'] == 1 ? '完成' : '未完成'}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$logs->links()}}
        </div>
    </div>
@endsection