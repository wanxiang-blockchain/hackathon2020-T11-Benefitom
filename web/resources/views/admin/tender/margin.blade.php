
@extends('layouts.admin')

@section('title', '拍品估价列表')

@section('content')
    <div class="page-title">
        <h2>拍品估价列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('tender/margin')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的用户手机号">
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
            总条数：
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>用户</th>
                    <th>保证金</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            <a href="javascript:;" data-id="{{$value->id}}" data-phone="{{$value->member->phone}}" class="btn btn-default btn-sm btn-operator btn-deduct" >扣除</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection
@push('scripts')
<script type="application/javascript">
    $(function () {
        $(".btn-deduct").on('click', function () {
            var id = $(this).data('id')
            var phone = $(this).data('phone')
            swal({
                    title: "确定要扣除" + phone + "的保证金吗?",
                    text: "扣除后，用户需要重新缴纳保证金方可参与竞拍！",
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
                        url: '/admin/tender/marginDeduct/' + id,
                        type:'POST',
                        dataType:'json',
                        success: function (res) {
                            setTimeout(function(){
                                swal({
                                    title: res.data,
                                    text: '',
                                    confirmButtonText: '确定'
                                }, function () {
                                    location.href = location.href
                                })
                            }, 500);
                        },
                        error: function () {
                            show_message('操作成功', '请检查您的网络参数', 'error');
                        }
                    });

                });
        })
    })
</script>
@endpush
