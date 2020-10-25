
@extends('layouts.admin')

@section('title', '会员管理列表')
@section('content')
    <div class="page-title">
        <h2>会员管理列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('admin/member/index')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入会员手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="is_lock" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('is_lock') == 2) selected @endif value="2">正常</option>
                                <option @if(request()->get('is_lock') == 1) selected @endif value="1">锁定</option>
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
            <p><a href="{{url('admin/member/create')}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>昵称</th>
                    <th>真实姓名</th>
                    <th>身份证</th>
                    <th>手机号</th>
                    <th>注册时间</th>
                    <th>最后修改时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($member as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['nickname']}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['code']}}</td>
                        <td>{{$value['phone']}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>
                            @if($value['is_lock'] == 1)
                                <a onclick="change(this)" data-url="{{route('member/change', ['id'=>$value['id'],'status'=>0])}}" class="btn btn-warning btn-sm">锁定</a>
                            @else
                                <a onclick="change(this)" data-url="{{route('member/change', ['id'=>$value['id'],'status'=>1])}}" class="btn btn-primary btn-sm">正常</a>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('member/edit', ['id'=>$value['id']])}}" class="btn btn-info btn-sm">编辑</a>
                            <a href="{{route('member/detail', ['id'=>$value['id']])}}" class="btn btn-warning btn-sm">财务明细</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$member->links()}}
        </div>
    </div>
    <script>
        function change(obj){
            var url = $(obj).data('url');
            swal({
                    title: "确定切换该状态吗?",
                    text: "切换后会员状态将改变",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "是",
                    cancelButtonText: "否",
                    closeOnConfirm: false,
                },
                function(){
                    $.ajax({
                        url:url,
                        type:'post',
                        dataType:'json',
                        success: function (res) {
                            if(res.code != 200) {
                                swal("切换失败", "请检查您的网络参数", "error");
                            } else {
                                swal("切换成功", "你已经切换状态", "success");
                                setTimeout(function () {
                                    window.location.reload();
                                }, 500);
                            }
                        },
                        error: function () {
                            swal("切换失败", "请检查您的网络参数", "erroe");
                        }
                    });

                });
        }
    </script>
@endsection