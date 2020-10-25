
@extends('layouts.admin')

@section('title', '充值审核列表')

@section('content')
    <div class="page-title">
        <h2>充值审核列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('finance/audit_list')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">充值用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">资产类型</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="asset_type" class="form-control">
                                <option value="" selected="">全部</option>
                                @foreach($assetTypes as $asset_type)
                                    <option @if(request()->get('asset_type') == $asset_type->code) selected @endif value="{{$asset_type->code}}">{{$asset_type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">当前状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="status" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('status') == 1) selected @endif value="1">审核中</option>
                                <option @if(request()->get('status') == 2) selected @endif value="2">已驳回</option>
                                <option @if(request()->get('status') == 3) selected @endif value="3">已审核</option>
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
                <a href="{{url('admin/finance/addRecharge')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加管理员充值</a>
                <a href="{{url('admin/excel/auditExport?=phone'.request()->get('phone').'&asset_type='.request()->get('asset_type').'&status='.request()->get('status   ').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i> 导出</a>
            </p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>充值用户</th>
                    <th>充值资产</th>
                    <th>充值数量</th>
                    <th>充值单价</th>
                    <th>解冻时间</th>
                    <th>提交时间</th>
                    <th>当前状态</th>
                    <th>操作</th>
                    <th>操作人</th>
                </tr>
                </thead>
                <tbody>
                @foreach($audit as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['phone']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['amount']}}</td>
                        <td>{{$value['balance']}}</td>
                        <td>
                            @if($value['unlock_time'] == "0000-00-00 00:00:00")
                                无冻结
                            @else
                                {{$value['unlock_time']}}
                            @endif
                        </td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            @if($value['status'] == 1)
                                <span class="label label-default">审核中</span>
                            @elseif($value['status'] == 2)
                                <span class="label label-default">已驳回</span>
                            @elseif($value['status'] == 3)
                                <span class="label label-success">已审核</span>
                            @endif
                        </td>
                        <td>
                            @if($value['status'] == 1)
                                <a onclick="lv_changes(this)" data-url="{{route('finance/audit', ['id'=>$value['id']])}}" class="btn btn-info    btn-sm">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif($value['status'] == 2)
                                {{--<a onclick="lv_delete(this)" data-url="{{route('manage/delete', ['id'=>$value['id']])}}" class="btn btn-info  btn-sm">查看驳回原因</a>--}}
                                <a  class="btn btn-info btn-sm"  data-toggle="modal" data-placement="top"  data-target="#myModal{{$value['id']}}" data-original-title="查看文章详情">查看驳回原因</a>

                            @elseif($value['status'] == 3)
                                <a class="btn btn-info  btn-sm">已完成</a>
                            @endif
                        </td>
                        <td>
                            @if($value['uname'])
                                {{$value['uname']}}
                            @else
                                无
                            @endif
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal{{$value['id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">驳回原因</h4>
                                </div>
                                <div class="modal-body">
                                    {!! $value['audit_reason'] !!}
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
            {{$audit->links()}}
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
                        $.post('/admin/finance/recharge_reject', {'reason':inputValue,'id':id }, function(result){
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
                                    location.href = '/admin/finance/audit_list';
                                }, 1000);
                            }
                        });
                    });
            })
        })
        function lv_changes(obj){
            var url = $(obj).data('url');
            swal({
                    title: "确定审核通过吗?",
                    text: "通过后对应的资产将充值到用户账户",
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
