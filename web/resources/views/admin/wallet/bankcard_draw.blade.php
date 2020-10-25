
@extends('layouts.admin')

@section('title', '银行卡提现列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">银行卡提现列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/bankcard/draws')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提取状态</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="stat" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option @if(request()->get('stat') === '1' ) selected @endif value="1">提取待审核</option>
                                    <option @if(request()->get('stat') === '2') selected @endif value="2">审核通过</option>
                                    <option @if(request()->get('stat') === '3') selected @endif value="3">提取驳回</option>
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
                <a href="{{url('admin/excel/bankcardDrawExport?=phone'.request()->get('phone').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i>导出</a>
                </p>
                <a id="pi-audit" class="btn btn-info btn-sm">批量通过</a>
                <a id="pi-reject" class="btn btn-default btn-sm">️批量驳回</a>
            </div>

        </div>
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th id="select-all" style="cursor: pointer;">全选</th>
                    <th>ID</th>
                    <th>手机</th>
                    <th>状态</th>
                    <th>收款账户</th>
                    <th>收款人姓名</th>
                    <th>总行</th>
                    <th>开户行</th>
                    <th>数量</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>
                            @if($value->stat == \App\Model\Btshop\BlockTiqu::STAT_INIT)
                                <input type="checkbox" class="item-id" value="{{$value->id}}">
                            @endif
                        </td>
                        <td>{{$value->id}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{\App\Model\Btshop\BankDraw::statLabel($value->stat)}}</td>
                        <td>{{$value->card}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value->headbank}}</td>
                        <td>{{$value->bank}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            @if($value->stat == \App\Model\Btshop\BlockTiqu::STAT_INIT)
                                <a onclick="lv_change(this)" data-url="{{route('admin/bankcard/draw/audit', ['ids'=>$value['id']])}}" class="btn btn-info btn-sm">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif ($value->stat == \App\Model\Btshop\BlockTiqu::STAT_DONE)
                                <a class="btn btn-info  btn-sm">已完成</a>
                            @else
                                <a  class="btn btn-info btn-sm"  data-toggle="modal" data-placement="top"  data-target="#myModal{{$value['id']}}" data-original-title="查看文章详情">查看驳回原因</a>
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
                                    {!! $value['note'] !!}
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
            {{$models->links()}}
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(function(){
                $('#select-all').click(function(){
                    $('.item-id').map(function(){
                        $(this).prop('checked', !this.checked)
                    })
                })
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
                            $.post('/admin/bankcard/draw/reject', {'reason':inputValue, 'ids':id }, function(result){
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
                $('#pi-reject').on('click', function () {
                    var ids = $('.item-id:checked').map(function(){
                        return $(this).val();
                    }).get().join(',');
                    console.log(ids)
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
                        $.post('/admin/bankcard/draw/reject', {'reason':inputValue, 'ids':ids }, function(result){
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
                $('#pi-audit').on('click', function () {
                    var ids = $('.item-id:checked').map(function(){
                        return $(this).val();
                    }).get().join(',');
                    console.log(ids)
                    swal({
                        title: "确定批量通过?",
                        text: "切换后将在前台动态改变状态,这将影响前后台状态显示",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "是",
                        cancelButtonText: "否",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function(){
                        $.ajax({
                            url: '/admin/bankcard/draw/audit/' + ids,
                            type:'post',
                            dataType:'json',
                            success: function (res) {
                                if(res.code != 200) {
                                    show_message('设置失败', '请检查您的网络参数', 'error');
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
                })
            })
        </script>
    @endpush
@endsection