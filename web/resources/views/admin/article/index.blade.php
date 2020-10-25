
@extends('layouts.admin')

@section('title', '文章列表')
@section('content')
    <div class="page-title">
        <h2>文章列表</h2>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <p><a href="{{route('article/create', $data['class'])}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>分类名称</th>
                    <th>文章标题</th>
                    <th>文章封面</th>
                    <th>创建时间</th>
                    <th>最后修改时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['article'] as $value)
                    <tr>

                        <td>{{$value['id']}}</td>
                        <td>{{$value->articlable->name}}</td>
                        <td>{{$value['title']}}</td>
                        <td><img src="{{asset('storage/'.$value->picture)}}" style="width: 20px;" alt=""></td>
                        <td>{{$value['created_at']}}</td>
                        <td>{{$value['updated_at']}}</td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="switch" onclick="is_show(this)"  data-url="{{route('article/change', ['id'=>$value['id']])}}"  @if($value['is_show']==1) checked="true"@endif/>
                                <span></span>
                            </label>
                        </td>
                        <td>
                            <a href="{{route('articlePreview',['id'=>$value['id']])}}" class="btn btn-default btn-sm"  data-target="#myModal{{$value['id']}}" target="_blank" data-original-title="查看文章详情"><i class="fa fa-eye"></i></a>
                            <a href="{{route('article/edit', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="编辑"><i class="fa fa-edit"></i></a>
                            <a onclick="lv_delete(this)" data-url="{{route('article/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm btn-operator" data-toggle="tooltip" data-placement="top" data-original-title="删除"><i class="fa fa-times"></i></a>
                        </td>
                    </tr>
                    <!-- Modal -->
                    {{--<div class="modal fade" id="myModal{{$value['id']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">--}}
                        {{--<div class="modal-dialog" role="document">--}}
                            {{--<div class="modal-content">--}}
                                {{--<div class="modal-header">--}}
                                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                                    {{--<h4 class="modal-title" id="myModalLabel">文章详情</h4>--}}
                                {{--</div>--}}
                                {{--<div class="modal-body">--}}
                                    {{--{!! $value['content'] !!}--}}
                                {{--</div>--}}
                                {{--<div class="modal-footer">--}}
                                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                @endforeach
                </tbody>
            </table>
            {{$data['article']->links()}}
        </div>
    </div>
@endsection