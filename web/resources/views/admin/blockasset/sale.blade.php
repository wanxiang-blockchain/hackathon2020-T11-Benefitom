
@extends('layouts.admin')

@section('title', 'ARTTBC售出列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">ARTTBC售出列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/block/asset/sale')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="stat" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option @if(request()->get('stat') === '0' ) selected @endif value="1">待打款</option>
                                    <option @if(request()->get('stat') === '1') selected @endif value="2">已打款</option>
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
                <a href="{{url('admin/excel/blockTiquExport?=phone'.request()->get('phone').'&type='.request()->get('type').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i>导出</a>
                </p>
            </div>

        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>MID</th>
                    <th>手机</th>
                    <th>状态</th>
                    <th>收款账户</th>
                    <th>收款人姓名</th>
                    <th>开户行</th>
                    <th>数量</th>
                    <th>打款金额</th>
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
                        <td>{{\App\Model\BlockSale::statLabel($value->stat)}}</td>
                        <td>{{$value->card}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value->bank}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->amount * 3}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            @if($value->stat == \App\Model\BlockSale::STAT_INIT)
                                <a onclick="lv_change(this)" data-url="{{route('admin/block/sale/audit', ['id'=>$value['id']])}}" class="btn btn-info    btn-sm">通过</a>
                            @elseif ($value->stat == \App\Model\BlockSale::STAT_DONE)
                                <a class="btn btn-info  btn-sm">已完成</a>
                            @else
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection