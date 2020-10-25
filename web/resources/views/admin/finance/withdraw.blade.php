
@extends('layouts.admin')

@section('title', '会员提现列表')

@section('content')
    <div class="page-title">
        <h2>会员提现列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('finance/withdraw')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">当前状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="status" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('status') == 1) selected @endif value="1">审核中</option>
                                <option @if(request()->get('status') == 2) selected @endif value="2">已驳回</option>
                                <option @if(request()->get('status') == 3) selected @endif value="3">已审核打款</option>
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
            <a href="{{route('finance/withdraw/create')}}" class="btn btn-default"> 添加管理员提现</a>
            <a href="{{url('admin/excel/withdrawExport?=phone'.request()->get('phone').'&type='.request()->get('type').'&asset_type='.request()->get('asset_type').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i> 导出</a>
            <a  class="btn btn-default"> 本页总计:{{$page_sum}}</a>
            <a  class="btn btn-default"> 总计:{{$withdraw_sum}}</a>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>提现用户</th>
                    <th>支付宝账号</th>
                    <th>支付宝用户名</th>
                    <th>提现金额</th>
                    <th>手续费</th>
                    <th>实际到账金额</th>
                    <th>申请时间</th>
                    <th>当前状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($withdraw as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->phone}}</td>
                        <td>{{$value['payment']}}</td>
                        <td>{{$value['aliname']}}</td>
                        <td>{{$value['money']}}</td>
                        <td>{{$value['fee']}}</td>
                        <td>{{$value['real_money']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            @if($value['status'] == 1)
                                <span class="label label-default">审核中</span>
                            @elseif($value['status'] == 2)
                                <span class="label label-default">已驳回</span>
                            @elseif($value['status'] == 3)
                                <span class="label label-success">已审核打款</span>
                            @endif
                        </td>
                        <td>
                            @if($value['status'] == 1)
                                <a onclick="lv_change(this)" data-url="{{route('finance/adopt', ['id'=>$value['id']])}}" class="btn btn-info    btn-sm">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif($value['status'] == 2)
                                {{--<a onclick="lv_delete(this)" data-url="{{route('manage/delete', ['id'=>$value['id']])}}" class="btn btn-info  btn-sm">查看驳回原因</a>--}}
                                <a  class="btn btn-info btn-sm"  data-toggle="modal" data-placement="top"  data-target="#myModal{{$value['id']}}" data-original-title="查看文章详情">查看驳回原因</a>

                            @elseif($value['status'] == 3)
                                <a class="btn btn-info  btn-sm">已完成</a>
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
                                    {!! $value['reason'] !!}
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
            {{$withdraw->links()}}
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
                        inputPlaceholder: "请输入驳回原因",
                        showLoaderOnConfirm: true
                    },
                    function(inputValue){
                        if (inputValue === false) return false;

                        if (inputValue === "") {
                            swal.showInputError("您没有输入驳回原因");
                            return false
                        }
                        $.post('/admin/finance/reject', {'reason':inputValue,'id':id }, function(result){
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
                                    location.href = '/admin/finance/withdraw';
                                }, 1000);
                            }
                        });
                    });
            })
        })
    </script>
    @endpush
@endsection
