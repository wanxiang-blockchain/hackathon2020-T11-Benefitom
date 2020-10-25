
@extends('layouts.admin')

@section('title', '文章分类列表')

@section('content')
    <div class="page-title">
        <h2>文章分类列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('category')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">名称</label>
                        <div class="col-sm-8 col-xs-12">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的名称">
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
            <p><a href="{{url('admin/category/create')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加分类</a></p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>名称</th>
                    <th>图片</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($category as $value)
                <tr>
                    <td>{{$value['id']}}</td>
                    <td>{{$value['name']}}</td>
                    <td>
                        @if($value['pictures'])
                            <img style="width: 30px;" src="{!! asset('storage/'.$value['pictures'][0]['url']) !!}" alt="">
                        @endif
                    </td>
                    <td>{{$value['created_at']}}</td>
                    <td>{{$value['updated_at']}}</td>
                    <td>
                        <a href="{{route('article/index', ['type'=>1,'id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="查看文章"><i class="fa fa-book"></i></a>
                        <a href="{{route('category/edit', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="编辑"><i class="fa fa-edit"></i></a>
                        @if($value['id'] > 5)
                        <a onclick="lv_delete(this)" data-url="{{route('category/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="删除"><i class="fa fa-times"></i></a>
                        @endif
                    </td>
                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection