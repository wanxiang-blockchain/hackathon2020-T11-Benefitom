
@extends('layouts.admin')

@section('title', 'artbc流水表')

@section('content')
    <div class="page-title">
        <h2>artbc流水表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('admin/artbc/logs')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">当前状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="stat" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('stat') === 0) selected @endif value="0">审核中</option>
                                <option @if(request()->get('stat') === 1) selected @endif value="1">已驳回</option>
                                <option @if(request()->get('stat') === 2) selected @endif value="2">已审核</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-lg-2">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
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
{{--            <a href="{{url('admin/withdrawAudit/add')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加管理员提现</a>--}}
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>用户</th>
                    <th>数量</th>
                    <th>时间</th>
                    <th>余额</th>
                    <th>类型</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value['amount']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['balance']}} </td>
                        <td>{{$value->type_label}} </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
    @push('scripts')
    <script type="text/javascript">

    </script>
    @endpush
@endsection
