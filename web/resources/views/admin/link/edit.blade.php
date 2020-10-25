@extends('layouts.admin')

@section('title', '编辑合作伙伴管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('link')}}"><i class="fa fa-reply"></i></a> 编辑合作伙伴</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                编辑合作伙伴
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('link/edit')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$link['id']}}">
                    {!! Form::bsNumber(['label' => '排序', 'name' => 'sort', 'value' => $link['sort'], 'placeholder' => "请输入排序", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '标题', 'name' => 'title', 'value' => $link['title'], 'placeholder' => "请输入合作伙伴标题", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => ' 链接', 'name' => 'link', 'value' => $link['link'], 'placeholder' => "请输入链接", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['name' => 'url', 'id' => 'filename2','value'=>$link['url'], 'title' => "上传图片",'url'=>[$link['url']]]) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="1" type="radio" class="iradio" @if($link['is_show']==1) checked="checked"@endif/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="0" type="radio" class="iradio" @if($link['is_show']==0) checked="checked"@endif/> 否</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-10">
                            <input type="submit" class="btn btn-danger btn-lg" value="提交">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
