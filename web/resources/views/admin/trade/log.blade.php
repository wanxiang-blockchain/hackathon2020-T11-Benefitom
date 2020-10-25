
@extends('layouts.admin')

@section('title', '成交记录')

@section('content')
    <div class="page-title">
        <h2>成交记录</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('trade/tradeLog')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">买方手机号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="buyPhone" id="" type="text" value="{{request()->get('buyPhone')}}" placeholder="请输入用户的手机号进行搜索">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">卖方手机号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="sellPhone" id="" type="text" value="{{request()->get('sellPhone')}}" placeholder="请输入用户的手机号进行搜索">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">价格</label>
                        <div class="col-xs-12 col-sm-8 col-lg-2 ">
                            <input type="number" class="form-control" name="beginPrice" value="{{request()->get('beginPrice')}}" placeholder="最小价格">
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-2 ">
                            <input type="number" class="form-control" name="endPrice" value="{{request()->get('endPrice')}}" placeholder="最大价格">
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
                    <th>成交编号</th>
                    <th>买方</th>
                    <th>卖方</th>
                    <th>资产名称</th>
                    <th>数量</th>
                    <th>成交单价</th>
                    <th>成交总价</th>
                    <th>成交时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tradeLog as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->buy_phone}}</td>
                        <td>{{$value->sell_phone}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value['amount']}}</td>
                        <td>{{$value['price']}}</td>
                        <td>{{$value['total']}}</td>
                        <td>{{$value['updated_at']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$tradeLog->links()}}
        </div>
    </div>
@endsection