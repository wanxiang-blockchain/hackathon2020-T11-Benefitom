
@extends('layouts.admin')

@section('title', '拍品管理列表')

@section('content')
    <div class="page-title">
        <h2>拍品管理列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('tender')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">名称</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">类型</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="type" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('type') == 0) selected @endif value="0">暗标</option>
                                <option @if(request()->get('type') == 1) selected @endif value="1">竞拍</option>
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
            <p><a href="{{url('admin/tender/create?nav=10|4')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加拍品</a></p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>名称</th>
                    <th>类型</th>
                    <th>估价时间</th>
                    <th>投标时间</th>
                    <th>状态</th>
                    <th>成交价</th>
                    <th>成交人</th>
                    <th>添加时间</th>
                    <th>相关列表</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['code']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value->type()}}</td>
                        <td>{!! $value->guessTime() !!}</td>
                        <td>{!! $value->tenderTime() !!}</td>
                        <td>{{$value->stat()}}</td>
                        <td>{{$value->dealPrice()}}</td>
                        <td>{{$value->dealMember()}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            @if($value->isDark())
                                <a href="{{route('tender/guess', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="估价列表">估价</a>
                            @endif
                            <a href="{{route('admin/tender/tender', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="投标列表">投标</a>
                        </td>
                        <td>
                            <a href="{{route('tender/edit', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="编辑"><i class="fa fa-edit"></i></a>
                            @if($value->stat != App\Model\Tender\Tender::STAT_DONE && (($value->isDark() && $value->stat == \App\Model\Tender\Tender::STAT_GUESS_COUNT_FINISHED) || $value->tender_end < \App\Utils\DateUtil::now()))
                                <a href="javascript:;" data-id="{{$value['id']}}" class="btn btn-default btn-sm btn-operator btn-finish" >结束</a>
                            @endif
                            {{--<a onclick="lv_delete(this)" data-url="{{route('tender/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="删除"><i class="fa fa-times"></i></a>--}}
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
        $(".btn-finish").on('click', function () {
            var id = $(this).data('id')
            swal({
                    title: "确定结束拍品吗?",
                    text: "结束后参拍用户即可退还保证金",
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
                        url: '/admin/tender/finish/' + id,
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
