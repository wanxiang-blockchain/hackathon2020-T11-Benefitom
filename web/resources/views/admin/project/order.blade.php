
@extends('layouts.admin')

@section('title', '项目订单列表')
@section('content')
    <div class="page-title">
        <h2>项目订单列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('projectOrder')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">订单号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="order_id" type="text" value="{{request()->get('order_id')}}" placeholder="请输入订单号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">所属项目</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="project_id" class="form-control">
                                <option value="" selected="">全部</option>
                                @foreach($project as $pro)
                                <option @if(request()->get('project_id') == $pro['id']) selected @endif value="{{$pro['id']}}">{{$pro['name']}}</option>
                                @endforeach
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
    <div class="panel panel-default">
        <div class="panel-body">
            <a href="{{url('admin/projectOrder/create')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加订单</a>
            <a href="{{url('admin/excel/projectOrderExport?order_id='.request()->get('order_id').'&project_id='.request()->get('project_id'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i> 导出订单</a>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>订单号</th>
                    <th>购买项目</th>
                    <th>购买人</th>
                    <th>价格</th>
                    <th>数量</th>
                    <th>支付方式</th>
                    <th>下单时间</th>
                    <th>支付时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $value)
                <tr>
                    <td>{{$value['id']}}</td>
                    <td>{{$value['order_id']}}</td>
                    <td>{{$value['project_name']}}</td>
                    <td>
                        {{$value->phone}}
                    </td>
                    <td>{{$value['price']}}</td>
                    <td>{{$value['quantity']}}</td>
                    <td>
                        @if($value['pay_type'] == 1)
                            余额
                        @elseif($value['pay_type'] == 2)
                            支付宝
                        @elseif($value['pay_type'] == 3)
                            微信
                        @elseif($value['pay_type'] == 4)
                            后台
                        @endif
                    </td>
                    <td>{{$value['created_at']}}</td>
                    <td>{{$value['updated_at']}}</td>
                    <td>
                        @if($value['status'] == 0)
                            <span class="label label-default">未支付</span>
                            @elseif($value['status'] == 1)
                            <span class="label label-success">已支付</span>
                            @elseif($value['status'] == 2)
                            <span class="label label-info">已完成</span>
                            @elseif($value['status'] == 3)
                            <span class="label label-danger">已关闭</span>
                        @endif
                    </td>
                    <td>
                        @if($value['status'] == 0)
                            <a onclick="lv_change(this)" data-url="{{route('projectOrder/change', ['id'=>$value['id'], 'status'=>1])}}" class="btn btn-warning btn-sm">付款</a>
                        @endif
                        @if($value['status'] == 1)
                            <a onclick="lv_change(this)" data-url="{{route('projectOrder/change', ['id'=>$value['id'],'status'=>2])}}" class="btn btn-danger btn-sm">完成</a>
                        @endif
                        @if($value['status'] == 2 || $value['status'] == 0)
                            <a onclick="lv_change(this)" data-url="{{route('projectOrder/change', ['id'=>$value['id'], 'status'=>3])}}" class="btn btn-primary btn-sm">关闭</a>
                        @endif

                    </td>
                </tr>
                    @endforeach
                </tbody>
            </table>
            {{$orders->links()}}
        </div>
    </div>
@endsection