@extends('layouts.admin')

@section('title', '新增分类管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('category')}}"><i class="fa fa-reply"></i></a> 新增分类</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增分类
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('category/create')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => old('name'), 'placeholder' => "请输入分类名称", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['name' => 'picture', 'id' => 'filename2','value'=>old('picture'), 'title' => "上传图片",'ext'=>'required','url'=>[]]) !!}
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
