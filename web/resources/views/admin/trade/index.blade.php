
@extends('layouts.admin')

@section('title', '委托记录')

@section('content')
    <div class="page-title">
        <h2>委托记录</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('trade/index')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入用户的手机号进行搜索">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">资产编号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="asset_type" id="" type="text" value="{{request()->get('asset_type')}}" placeholder="请输入资产编号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="status" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('status') == 4) selected @endif value="4">挂单</option>
                                <option @if(request()->get('status') == 1) selected @endif value="1">部分成交</option>
                                <option @if(request()->get('status') == 2) selected @endif value="2">成交</option>
                                <option @if(request()->get('status') == 3) selected @endif value="3">撤销</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">类型</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="type" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('status') == 1) selected @endif value="1">买入</option>
                                <option @if(request()->get('status') == 2) selected @endif value="2">卖出</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">价格</label>
                        <div class="col-xs-12 col-sm-8 col-lg-2 ">
                            <input type="number" class="form-control" name="beginPrice" value="{{request()->get('beginPrice')}}" placeholder="最小价格">
                        </div>
                        <div class="col-xs-12 col-sm-8 col-lg-2 ">
                            <input type="number" class="form-control" name="endPrice" value="{{request()->get('endPrice')}}" placeholder="最大价格">
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
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>委托编号</th>
                    <th>用户手机</th>
                    <th>资产名称</th>
                    <th>价格</th>
                    <th>挂单数量</th>
                    <th>成交数量</th>
                    <th>类型</th>
                    <th>挂单时间</th>
                    <th>成交时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($trade as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->phone}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value['price']}}</td>
                        <td>{{$value['quantity']}}</td>
                        <td>{{$value['quantity'] - $value['amount']}}</td>
                        <td>
                            @if($value['type'] == 1)
                                <button class="btn btn-warning btn-sm">买入</button>
                            @else
                                <button class="btn btn-info btn-sm">卖出</button>
                            @endif
                        </td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            @if($value['status'] == 2)
                               {{$value['updated_at']}}
                            @else
                               #
                            @endif
                        </td>
                        <td>
                            @if($value['status'] == 0)
                                <button class="btn btn-warning btn-sm">挂单</button>
                            @elseif( $value['status'] == 1)
                                <button class="btn btn-danger btn-sm">部分成交</button>
                            @elseif($value['status'] == 2)
                                <button class="btn btn-success btn-sm">成交</button>
                            @else
                                <button class="btn btn-primary btn-sm">已撤销</button>
                            @endif
                        </td>
                        <td>
                            @if($value['status'] == 0 || $value['status'] == 1)
                                <a  onclick="revoked(this)" data-url="{{route('trade/revoked', ['id'=>$value['id']])}}"  class="btn btn-danger btn-sm">撤销</a>
                            @endif
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal{{$value['id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">文章详情</h4>
                                </div>
                                <div class="modal-body">
                                    {!! $value['content'] !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
            {{$trade->links()}}
        </div>
    </div>
    <script>
        function revoked(obj){
            var url = $(obj).data('url');
            swal({
                    title: "确定撤销该委托吗?",
                    text: "切换后将在前台动态改变状态,这将影响前后台状态显示",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "是",
                    cancelButtonText: "否",
                    closeOnConfirm: false,
                },
                function(){
                    $.ajax({
                        url:url,
                        type:'post',
                        dataType:'json',
                        success: function (res) {
                            if(res.code != 200) {
                                show_message('撤销失败', '交易已经完成', 'error');
                            } else {
                                show_message('撤销成功', '你已经设置成功', 'success');
                                setTimeout(function () {
                                    window.location.reload();
                                }, 500);
                            }
                        },
                        error: function () {
                            show_message('撤销失败', '请检查您的网络参数', 'error');
                        }
                    });

                });
        }
    </script>
@endsection