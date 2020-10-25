
@extends('layouts.admin')

@section('title', '资产列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">资产列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('assets')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">资产名称</label>
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
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">资产状态</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="is_lock" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option @if(request()->get('is_lock') == 0) selected @endif value="0">正常</option>
                                    <option @if(request()->get('is_lock') == 1) selected @endif value="1">冻结</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-2 col-lg-2">
                                <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>用户</th>
                    <th>资产</th>
                    <th>数量</th>
                    <th>成本</th>
                    <th>状态</th>
                    <th>解冻时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->account->member->phone}}</td>
                        <td>{{$value->asset_type}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->cost}}</td>
                        <td>{{$value->lockText()}}</td>
                        <td>{{$value->unlock_time}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
    <script>
    </script>
@endsection