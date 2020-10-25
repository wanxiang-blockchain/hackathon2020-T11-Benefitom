
@extends('layouts.admin')

@section('title', 'ArTBC提币列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">ArTBC提币列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/block/asset/tibis')}}" method="get" class="form-horizontal" role="form">
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
                                    <option @if(request()->get('stat') === '0' ) selected @endif value="0">待转币</option>
                                    <option @if(request()->get('stat') === '1') selected @endif value="1">已转币</option>
                                    <option @if(request()->get('stat') === '2') selected @endif value="2">已驳回</option>
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
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>MID</th>
                    <th>手机</th>
                    <th>状态</th>
                    <th>以太账号</th>
                    <th>数量</th>
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
                        <td>{{\App\Model\BlockTibi::statLabel($value->stat)}}</td>
                        <td>{{$value->addr}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            @if($value->stat == \App\Model\BlockSale::STAT_INIT)
                                <a onclick="lv_change(this)" data-url="{{route('admin/block/tibi/audit', ['id'=>$value['id']])}}" class="btn btn-info    btn-sm">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif ($value->stat == \App\Model\BlockTibi::STAT_DONE)
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
                            $.post('/admin/block/tibi/reject', {'reason':inputValue, 'id':id }, function(result){
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