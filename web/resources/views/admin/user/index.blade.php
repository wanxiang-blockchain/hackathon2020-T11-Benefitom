
@extends('layouts.admin')

@section('title', '角色列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">角色列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('manage/user')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">角色</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="type" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option @if(request()->get('type') == 1) selected @endif value="1">管理员</option>
                                    <option @if(request()->get('type') == 2) selected @endif value="2">业务员</option>
                                    <option @if(request()->get('type') == 3) selected @endif value="2">财务</option>
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
        <div class="panel-body">
            <p><a href="{{url('admin/manage/create')}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>角色</th>
                    <th>名称</th>
                    <th>手机</th>
                    <th>创建时间</th>
                    <th>最后修改时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($user as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->role->name}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['phone']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>
                            <a href="{{route('manage/edit', ['id'=>$value['id']])}}" class="btn btn-info btn-sm">编辑</a>
                            @if($value['id'] != 1)
                            <a onclick="lv_delete(this)" data-url="{{route('manage/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm">删除</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$user->links()}}
        </div>
    </div>
    <script>
    </script>
@endsection