
@extends('layouts.admin')

@section('title', '产品管理列表')

@section('content')
    <div class="page-title">
        <h2>产品管理列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('project')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">名称</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="is_show" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('is_show') == 1) selected @endif value="1">开始</option>
                                <option @if(request()->get('is_show') == 2) selected @endif value="2">结束</option>
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
            <p><a href="{{url('admin/project/create?nav=2|2')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加项目</a></p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>缩略图</th>
                    <th>名称</th>
                    <th>价格</th>
                    <th>数量</th>
                    <th>总量</th>
                    <th>已售出</th>
                    <th>开始时间</th>
                    <th>结束时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($projects as $value)
                <tr>
                    <td>{{$value['id']}}</td>
                    <td><img src="{{asset('storage/'.$value['picture'])}}" style="width: 20px;" alt=""></td>
                    <td>{{$value['name']}}</td>
                    <td>{{$value['price']}}</td>
                    <td>{{$value['limit']}}</td>
                    <td>{{$value['total']}}</td>
                    <td>{{$value['position']}}</td>
                    <td>{{$value['start']}}</td>
                    <td>{{$value['end']}}</td>
                    <td>
                        <a href="{{route('project/edit', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="编辑"><i class="fa fa-edit"></i></a>
                        <a href="{{route('article/index', ['type'=>2,'id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="查看文章"><i class="fa fa-book"></i></a>

                        @if($value['is_show'] == 1)
                            <a onclick="lv_change(this)" data-url="{{route('project/change', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="使该项切换成结束状态"><i class="fa fa-clock-o"></i></a>
                        @else
                            <a onclick="lv_change(this)" data-url="{{route('project/change', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="使该项切换成开始状态"><i class="fa fa-clock-o"></i></a>
                        @endif
                        <a onclick="lv_delete(this)" data-url="{{route('project/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="删除"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                    @endforeach
                </tbody>
            </table>
    {{$projects->links()}}
        </div>
    </div>
@endsection
