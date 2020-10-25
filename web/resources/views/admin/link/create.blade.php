@extends('layouts.admin')

@section('title', '新增合作伙伴管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('link')}}"><i class="fa fa-reply"></i></a> 新增合作伙伴</h2>
    </div>
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增合作伙伴
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('link/create')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    {!! Form::bsNumber(['label' => '排序', 'name' => 'sort', 'value' => old('sort'), 'placeholder' => "请输入排序", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '标题', 'name' => 'title', 'value' => old('title'), 'placeholder' => "请输入合作伙伴标题", 'ext'=>'required']) !!}
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
