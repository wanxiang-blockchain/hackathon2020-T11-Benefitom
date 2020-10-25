
@extends('layouts.admin')

@section('title', '课程列表')

@section('content')
    <div class="page-title">
        <h2>课程列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('tender/courses')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">名称</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="stat" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('stat') == 0) selected @endif value="0">未发布</option>
                                <option @if(request()->get('stat') == 1) selected @endif value="1">已发布</option>
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
            <p><a href="{{url('/admin/tender/course/create?nav=10|9')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加课程</a></p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>名称</th>
                    <th>状态</th>
                    <th>摘要</th>
                    <th>视频截图</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value->statLabel()}}</td>
                        <td>{{$value['summary']}}</td>
                        <td><img src="{{$value->poster}}" width="50"></td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            <a href="{{route('/admin/tender/course/edit', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="编辑"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" data-name="{{$value->name}}" data-id="{{$value->id}}" class="del btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="删除"><i class="fa fa-times"></i></a>

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
        $('.del').on('click', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            swal({
                title: '您确定要删除' + name + '?',
                text:  '您确定要删除' + name + '?',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "是",
                cancelButtonText: "否",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function () {
                $.ajax({
                    url: '/admin/tender/course/del/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        swal({
                            title: res.data,
                            text: '',
                            confirmButtonText: '确定'
                        }, function () {
                            location.href = location.href
                        })
                    },error: function (err) {
                        swal(err)
                    }
                })
            })
        })
    })
</script>
@endpush
