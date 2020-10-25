
@extends('layouts.admin')

@section('title', '小红花流水')

@section('content')
    <div class="page-title">
        <h2>小红花流水</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('tender/flow')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">奖项</label>
                        <div  class="col-xs-12 col-sm-8 col-lg-9">
                            <select name="type"  class="form-control"  >
                                <option value="" selected="">全部</option>
                                @foreach(\App\Model\Tender\TenderFlow::types() as $type => $label)
                                    <option @if(request()->get('type') == $type) selected @endif value="{{$type}}">{{$label}}</option>
                                @endforeach
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
                    <th>用户</th>
                    <th>类型</th>
                    <th>数额</th>
                    <th>余额</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->defCon()}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->after_amount}}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection