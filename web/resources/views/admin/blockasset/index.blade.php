
@extends('layouts.admin')

@section('title', '艺行通流水')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">艺行通流水</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/block/asset/logs')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">变更类型</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="type" class="form-control">
                                    <option value="" selected="">全部</option>
                                    @foreach(\App\Model\Btshop\BlockAssetLog::typeMaps() as $key => $value)
                                        <option value="{{$key}}"  @if(request()->get('type') == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">币</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="code" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option value="300001"  @if(request()->get('code') == '300001') selected @endif>ArTBC</option>
                                    <option value="300002"  @if(request()->get('code') == '300002') selected @endif>ARTTBC</option>
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
                <a class="btn btn-default"> 总计:{{$totalSum}}</a>
                </p>
            </div>

        </div
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>MID</th>
                    <th>手机</th>
                    <th>code</th>
                    <th>类型</th>
                    <th>数量</th>
                    <th>余额</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->member->id}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{\App\Model\Btshop\BlockAssetLog::codeToName($value->code)}}</td>
                        <td>{{\App\Model\Btshop\BlockAssetLog::fetchTypeLable($value->type)}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->balance}}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection