
@extends('layouts.admin')

@section('title', 'artbc提取表')

@section('content')
    <div class="page-title">
        <h2>artbc流水表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('admin/artbc/ti')}}" method="get" class="form-horizontal" role="form">
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
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>用户</th>
                    <th>数量</th>
                    <th>手续费</th>
                    <th>实转数量</th>
                    <th>时间</th>
                    <th>余额</th>
                    <th>地址</th>
                    <th>类型</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value['amount']}}</td>
                        <td>0</td>
                        <td>{{$value['amount']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['balance']}} </td>
                        <td>{{$value['eth_addr']}} </td>
                        <td>{{$value->type_label}} </td>
                        <td>
                            @if($value['stat'] == 0)
                                <a onclick="lv_changes(this)" data-url="/admin/artbc/audit" data-id="{{$value['id']}}" class="btn btn-info btn-sm">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @else
                                {{$value->stat_label}}
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
            $('.reject').on('click', function () {
                var id = $(this).data('id');
                swal({
                        title: "请输入驳回原因:",
                        text: "",
                        type: "input",
                        inputType:'text',
                        showCancelButton: true,
                        closeOnConfirm: false,
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
                        $.post('/admin/artbc/audit', {'reason':inputValue, 'id':id, stat: 2 }, function(result){
                            if(result.code!=200){
                                swal.showInputError(result.message);
                            }else{
                                swal({
                                    title: "",
                                    text:"操作成功",
                                    type: "success",
                                    confirmButtonText: "确定",
                                })
                                setTimeout(function(){
                                    window.location.reload();
                                }, 1000);
                            }
                        });
                    });
            })
        })
        function lv_changes(obj){
            var id = $(obj).data('id');
            swal({
                    title: "确定审核通过吗?",
                    text: "通过后对应的资产将提现到用户账户",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "是",
                    cancelButtonText: "否",
                    closeOnConfirm: false,
                },
                function(){
                    $.ajax({
                        url: '/admin/artbc/audit',
                        data: {id: id, stat: 1},
                        type:'post',
                        dataType:'json',
                        success: function (res) {
                            if(res.code != 200) {
                                show_message('设置失败', res.data,'error');
                            } else {
                                show_message('设置成功', '你已经设置成功', 'success');
                                setTimeout(function () {
                                    window.location.reload();
                                }, 500);
                            }
                        },
                        error: function () {
                            show_message('设置失败', '请检查您的网络参数', 'error');
                        }
                    });

                });
        }

    </script>
    @endpush
@endsection
