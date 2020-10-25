
@extends('layouts.admin')

@section('title', '合作伙伴列表')

@section('content')
    <div class="page-title">
        <h2>合作伙伴列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('link')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">标题</label>
                        <div class="col-sm-8 col-xs-12">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的标题">
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
            <p><a href="{{url('admin/link/create')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加合作伙伴</a></p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>排序</th>
                    <th>标题</th>
                    <th>链接</th>
                    <th>图片</th>
                    <th>是否开启</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($link as $value)
                <tr>
                    <td>{{$value['sort']}}</td>
                    <td>{{$value['title']}}</td>
                    <td>{{$value['link']}}</td>
                    <td>
                        @if($value['url'])
                            <img style="width: 30px;" src="{!! asset('storage/'.$value['url']) !!}" alt="">
                        @endif
                    </td>
                    <td>
                        @if($value['is_show'] == 0)
                            <span class="label label-default">关闭</span>
                        @elseif($value['is_show'] == 1)
                            <span class="label label-success">开启</span>
                        @endif
                    </td>
                    <td>{{$value['created_at']}}</td>
                    <td>
                        <a href="{{route('link/edit', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="编辑"><i class="fa fa-edit"></i></a>
                        <a onclick="lv_delete(this)" data-url="{{route('link/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="删除"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')

@endpush