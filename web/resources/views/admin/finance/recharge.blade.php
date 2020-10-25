
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
                <form action="{{route('finance/recharge')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">账号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入用户的手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">充值类型</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="type" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('type') == 1) selected @endif value="1">管理员充值</option>
                                <option @if(request()->get('type') == 2) selected @endif value="2">会员充值</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">资产类型</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="asset_type" class="form-control">
                                <option value="" selected="">全部</option>
                                @foreach($assetTypes as $asset_type)
                                    <option @if(request()->get('asset_type') == $asset_type->code) selected @endif value="{{$asset_type->code}}">{{$asset_type->name}}</option>
                                @endforeach
                            </select>
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
            <p>
                <a href="{{url('admin/excel/financeExport?=phone'.request()->get('phone').'&type='.request()->get('type').'&asset_type='.request()->get('asset_type').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i> 导出</a>
                <a  class="btn btn-default"> 本页总计:{{$page_sum}}</a>
                <a  class="btn btn-default"> 总计:{{$balance_sum}}</a>
            </p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>充值用户</th>
                    <th>充值类型</th>
                    <th>充值财产</th>
                    <th>充值金额</th>
                    <th>充值股份数量</th>
                    <th>创建时间</th>
                    <th>充值描述</th>
                </tr>
                </thead>
                <tbody>
                @foreach($recharge as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value->assetType->name}}</td>
                        <td>{{$value['balance']}}</td>
                        <td>{{$value['amount']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['content']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$recharge->links()}}
        </div>
    </div>
@endsection