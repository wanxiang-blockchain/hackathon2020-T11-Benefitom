
@extends('layouts.admin')

@section('title', '邀请列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">邀请总数：{{$total}}</h3>
        </div>
        {{--<div class="search_main">--}}
            {{--<div class="panel panel-info">--}}
                {{--<div class="panel-heading">筛选</div>--}}
                {{--<div class="panel-body">--}}
                    {{--<form action="{{route('agent')}}" method="get" class="form-horizontal" role="form">--}}
                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>--}}
                            {{--<div class="col-xs-12 col-sm-8 col-lg-9">--}}
                                {{--<input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="col-xs-12 col-sm-2 col-lg-2">--}}
                                {{--<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>手机</th>
                    <th>邀请时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['phone']}}</td>
                        <td>{{$value['created_at']}}</td>
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