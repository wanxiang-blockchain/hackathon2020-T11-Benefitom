
@extends('layouts.admin')
<link  href="/js/viewerjs/viewer.min.css" rel="stylesheet">
@section('title', '会员管理列表')
@section('content')
    <div class="page-title">
        <h2>会员管理列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('admin/profiles')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入会员手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">身份证号</label>
                        <div class="col-xs-12 col-sm-8 col-lg-9">
                            <input class="form-control" name="idno" id="" type="text" value="{{request()->get('idno')}}" placeholder="请输入身份证号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name="verified" class="form-control">
                                <option value="" selected="">全部</option>
                                <option @if(request()->get('verified') === '0') selected @endif value="0">待审核</option>
                                <option @if(request()->get('verified') === '1') selected @endif value="1">通过</option>
                                <option @if(request()->get('verified') === '2') selected @endif value="2">未通过</option>
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
    <div class="panel panel-default" id="images">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>手机号</th>
                    <th>姓名</th>
                    <th>身份证</th>
                    <th>身份证正面照</th>
                    <th>身份证背面照</th>
                    <th>手持身份证照</th>
                    <th>性别</th>
                    <th>提审时间</th>
                    <th>状态</th>
                    <th>审核备注</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['idno']}}</td>
                        <td><img src="{{$value['id_img']}}" width="100" height="100" /> </td>
                        <td><img src="{{$value['id_back_img']}}" width="100" height="100" /> </td>
                        <td><img src="{{$value['id_hold_img']}}" width="100" height="100" /> </td>
                        <td>{{\App\Model\ProfileLog::sexLabel($value['sex'])}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{\App\Model\ProfileLog::verifyLabel($value['verified'])}}</td>
                        <td>{{$value['note']}}</td>
                        <td>
                            @if($value['verified'] == 0)
                                <a onclick="lv_change(this)" data-url="{{route('admin/profile/audit', ['id'=>$value['id']])}}" class="btn btn-info btn-sm">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif($value['verified'] == 1)
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm revert">撤回</a>
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
    <script src="/js/viewerjs/viewer.min.js"></script>
    <script>
        const gallery = new Viewer(document.getElementById('images'));
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
                        $.post('/admin/profile/reject', {'note':inputValue, 'id':id }, function(result){
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
            $('.revert').on('click', function () {
                var id = $(this).data('id');
                swal({
                        title: "请输入撤回原因:",
                        text: "",
                        type: "input",
                        inputType:'text',
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        confirmButtonText: "确定",
                        cancelButtonText: " 取消",
                        inputPlaceholder: "请输入撤原因",
                        showLoaderOnConfirm: true
                    },
                    function(inputValue){
                        if (inputValue === false) return false;

                        if (inputValue === "") {
                            swal.showInputError("您没有输入撤回原因");
                            return false
                        }
                        $.post('/admin/profile/revert', {'note':inputValue, 'id':id }, function(result){
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
@endsection