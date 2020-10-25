
@extends('layouts.admin')

@section('title', '促销中心提货列表')

@section('content')
    <div class="page-title">
        <h2>促销中心提货列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('btshop/delivery')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提货用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">当前状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="stat" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('stat') == 1) selected @endif value="1">待发货</option>
                                <option @if(request()->get('stat') == 2) selected @endif value="2">发货</option>
                                <option @if(request()->get('stat') == 3) selected @endif value="3">已驳回</option>
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
            <a href="{{url('admin/excel/btshopDeliveryExport?=phone'.request()->get('phone').'&stat='.request()->get('stat').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i>导出</a>
            </p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>提货用户</th>
                    <th>提货产品</th>
                    <th>提货数量</th>
                    <th>收件人</th>
                    <th>收件人手机号</th>
                    <th>省</th>
                    <th>市</th>
                    <th>区/县</th>
                    <th>地址</th>
                    <th>申请时间</th>
                    <th>当前状态</th>
                    <th>操作</th>
                    <th>备注</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->product->name}}</td>
                        <td>{{$value->order->amount}}</td>
                        <td>{{$value->receiver}}</td>
                        <td>{{$value->receive_nationcode . $value->receive_phone}}</td>
                        <td>{{$value->receive_province}}</td>
                        <td>{{$value->receive_city}}</td>
                        <td>{{$value->receive_area}}</td>
                        <td>{{$value->receive_addr}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            <span class="label label-default">
                                {{\App\Model\Btshop\BtshopDelivery::statLabel($value->stat)}}
                            </span>
                        </td>
                        <td>
                            @if($value['stat'] == \App\Model\Btshop\BtshopDelivery::STAT_INIT)
                                <a data-url="{{route('btshop/delivery/audit', ['id'=>$value['id']])}}" class="btn btn-info audit btn-sm">发货</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif($value['stat'] == \App\Model\Btshop\BtshopDelivery::STAT_REJECT)
                                <a class="btn btn-info  btn-sm">已驳回</a>
                            @elseif($value['stat'] == \App\Model\Btshop\BtshopDelivery::STAT_DONE)
                                <a class="btn btn-info  btn-sm">已发货</a>
                            @endif
                        </td>
                        <td>
                            {{$value->note}}
                            @if($value['stat'] != 1)
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm mod-note">修改</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                    }
                });

                $('.audit').on('click', function () {
                    var url = $(this).data('url');
                    swal({
                            title: "请输入快递单号及备注:",
                            text: "",
                            type: "input",
                            inputType:'text',
                            showCancelButton: true,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true,
                            animation: "slide-from-top",
                            confirmButtonText: "确定",
                            cancelButtonText: " 取消",
                            inputPlaceholder: "请输入快递单号及备注"
                        },
                        function(inputValue){
                            if (inputValue === false) return false;

                            if (inputValue === "") {
                                swal.showInputError("您没有输入输入快递单号");
                                return false
                            }
                            $.post(url, {'note':inputValue }, function(result){
                                if(result.code!=200){
                                    swal.showInputError(result.data);
                                }else{
                                    swal({
                                        title: "",
                                        text:"操作成功",
                                        type: "success",
                                        confirmButtonText: "确定",
                                    })
                                    setTimeout(function(){
                                        location.href = location.href;
                                    }, 1000);
                                }
                            });
                        });
                })

                $('.reject').on('click', function () {
                    var id = $(this).data('id');
                    swal({
                            title: "请输入驳回原因:",
                            text: "",
                            type: "input",
                            inputType:'text',
                            showCancelButton: true,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true,
                            animation: "slide-from-top",
                            confirmButtonText: "确定",
                            cancelButtonText: " 取消",
                            inputPlaceholder: "请输入驳回原因"
                        },
                        function(inputValue){
                            if (inputValue === false) return false;

                            if (inputValue === "") {
                                swal.showInputError("您没有输入驳回原因");
                                return false
                            }
                            $.post('/admin/btshop/delivery/reject/' + id, {'note':inputValue }, function(result){
                                if(result.code!=200){
                                    swal.showInputError(result.data);
                                }else{
                                    swal({
                                        title: "",
                                        text:"操作成功",
                                        type: "success",
                                        showLoaderOnConfirm: true,
                                        confirmButtonText: "确定",
                                    })
                                    setTimeout(function(){
                                        location.href = location.href;
                                    }, 1000);
                                }
                            });
                        });
                })

                // 修改备注

                $('.mod-note').on('click', function () {
                    var url = '/admin/btshop/delivery/note/' + $(this).data('id')
                    swal({
                            title: "请输入快递单号及备注:",
                            text: "",
                            type: "input",
                            inputType:'text',
                            showCancelButton: true,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true,
                            animation: "slide-from-top",
                            confirmButtonText: "确定",
                            cancelButtonText: " 取消",
                            inputPlaceholder: "请输入快递单号及备注"
                        },
                        function(inputValue){
                            if (inputValue === false) return false;

                            if (inputValue === "") {
                                swal.showInputError("您没有输入输入快递单号");
                                return false
                            }
                            $.post(url, {'note':inputValue }, function(result){
                                if(result.code!=200){
                                    swal.showInputError(result.data);
                                }else{
                                    swal({
                                        title: "",
                                        text:"操作成功",
                                        type: "success",
                                        confirmButtonText: "确定",
                                    })
                                    setTimeout(function(){
                                        location.href = location.href;
                                    }, 1000);
                                }
                            });
                        });
                })

            })
        </script>
    @endpush
@endsection
