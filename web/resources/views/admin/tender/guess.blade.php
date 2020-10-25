
@extends('layouts.admin')

@section('title', '拍品估价列表')

@section('content')
    <div class="page-title">
        <h2>拍品估价列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('tender/guess', ['id' => request()->get('id')])}}" method="get" class="form-horizontal" role="form">
                    <input type="hidden" name="id" value="{{request()->get('id')}}">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">奖项</label>
                        <div  class="col-xs-12 col-sm-8 col-lg-9">
                            <select name="winner_type"  class="form-control"  >
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('winner_type') == 0) selected @endif value="0">无奖</option>
                                <option @if(request()->get('winner_type') == 1) selected @endif value="1">火眼晴金奖</option>
                                <option @if(request()->get('winner_type') == 2) selected @endif value="2">睛金奖</option>
                            </select>
                        </div>
                    </div>
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
                    <th>估价价格</th>
                    <th>是否中奖</th>
                    <th>估价时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->tender->code}}</td>
                        <td>{{$value->tender->name}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->tender_price}}</td>
                        <td>{{$value->winnerType()}}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection