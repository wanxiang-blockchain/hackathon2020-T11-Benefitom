
@extends('layouts.admin')

@section('title', '拍品投标列表')

@section('content')
    <div class="page-title">
        <h2>拍品投标列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('tender/guess', ['id' => request()->get('id')])}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的用户手机号">
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
            总条数：
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>名称</th>
                    <th>用户</th>
                    <th>出价</th>
                    <th>出价时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->tender->code}}</td>
                        <td>{{$value->tender->name}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->price}}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection