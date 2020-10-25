
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
                <form action="{{route('tender/withdraw')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">当前状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="stat" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('stat') == 1) selected @endif value="1">审核中</option>
                                <option @if(request()->get('stat') == 2) selected @endif value="2">已驳回</option>
                                <option @if(request()->get('stat') == 3) selected @endif value="3">已审核打款</option>
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
    {{--<div class="panel panel-default">--}}
        {{--<div class="panel-body">--}}
            {{--<a href="{{route('finance/withdraw/create')}}" class="btn btn-default"> 添加管理员提现</a>--}}
            {{--<a href="{{url('admin/excel/withdrawExport?=phone'.request()->get('phone').'&type='.request()->get('type').'&asset_type='.request()->get('asset_type').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i> 导出</a>--}}
            {{--<a  class="btn btn-default"> 本页总计:{{$page_sum}}</a>--}}
            {{--<a  class="btn btn-default"> 总计:{{$withdraw_sum}}</a>--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>提现用户</th>
                    <th>提现金额</th>
                    <th>提现账号</th>
                    <th>收款人姓名</th>
                    <th>收款账号运行</th>
                    <th>申请时间</th>
                    <th>备注</th>
                    <th>当前状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $model)
                    <tr>
                        <td>{{$model['id']}}</td>
                        <td>{{$model->member->phone}}</td>
                        <td>{{$model->amount}}</td>
                        <td>{{$model->card}}</td>
                        <td>{{$model->name}}</td>
                        <td>{{$model->bank}}</td>
                        <td>{{$model->created_at}}</td>
                        <td>{{$model->note}}</td>
                        <td>
                            @if($model->stat == 0)
                                <span class="label label-default">审核中</span>
                            @elseif($model->stat == 2)
                                <span class="label label-default">已驳回</span>
                            @elseif($model->stat == 1)
                                <span class="label label-success">已审核打款</span>
                            @endif
                        </td>
                        <td>
                            @if($model['stat'] == 0)
                                <a onclick="lv_change(this)" data-url="{{route('tender/adopt', ['id'=>$model['id']])}}" class="btn btn-info    btn-sm">通过</a>
                                <a data-id="{{ $model['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @else
                                <a class="btn btn-info  btn-sm">已完成</a>
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
                        inputPlaceholder: "请输入驳回原因",
                        showLoaderOnConfirm: true
                    },
                    function(inputValue){
                        if (inputValue === false) return false;

                        if (inputValue === "") {
                            swal.showInputError("您没有输入驳回原因");
                            return false
                        }
                        $.post('/admin/tender/reject', {'note':inputValue,'id':id }, function(result){
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
                                    location.href = '/admin/tender/withdraw';
                                }, 1000);
                            }
                        });
                    });
            })
        })
    </script>
    @endpush
@endsection
