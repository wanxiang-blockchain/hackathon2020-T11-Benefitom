@extends('layouts.admin')

@section('title', '新增幻灯片管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('slide')}}"><i class="fa fa-reply"></i></a> 新增幻灯片</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增幻灯片
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('slide/create')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    {!! Form::bsNumber(['label' => '排序', 'name' => 'sort', 'value' => old('sort'), 'placeholder' => "请输入排序", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '标题', 'name' => 'title', 'value' => old('title'), 'placeholder' => "请输入幻灯片标题", 'ext'=>'required']) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">位置</label>
                        <div class="col-md-10">
                            <select name="pos" class="form-control">
                                <option value="" selected="">位置</option>
                                <option value="0">益通云</option>
                                <option value="1">艺奖堂</option>
                            </select>
                        </div>
                    </div>

                    {!! Form::bsText(['label' => ' 链接', 'name' => 'link', 'value' => old('link'), 'placeholder' => "请输入链接", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['name' => 'url', 'id' => 'filename2','value'=>old('url'), 'title' => "上传图片",'url'=>[]]) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="1" type="radio" class="iradio"/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="0" type="radio" class="iradio" checked="checked"/> 否</label>
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
