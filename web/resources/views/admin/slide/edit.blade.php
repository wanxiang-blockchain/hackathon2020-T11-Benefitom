@extends('layouts.admin')

@section('title', '编辑幻灯片管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('slide')}}"><i class="fa fa-reply"></i></a> 编辑幻灯片</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                编辑幻灯片
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('slide/edit')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$slide['id']}}">
                    {!! Form::bsNumber(['label' => '排序', 'name' => 'sort', 'value' => $slide['sort'], 'placeholder' => "请输入排序", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '标题', 'name' => 'title', 'value' => $slide['title'], 'placeholder' => "请输入幻灯片标题", 'ext'=>'required']) !!}


                    <div class="form-group">
                        <label class="col-md-2 control-label">位置</label>
                        <div class="col-md-10">
                            <select name="pos" class="form-control">
                                <option value="">位置</option>
                                <option value="0" @if($slide['pos'] == 0) selected @endif >益通云</option>
                                <option value="1" @if($slide['pos'] == 1) selected @endif >艺奖堂</option>
                            </select>
                        </div>
                    </div>

                    {!! Form::bsText(['label' => ' 链接', 'name' => 'link', 'value' => $slide['link'], 'placeholder' => "请输入链接", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['name' => 'url', 'id' => 'filename2','value'=>$slide['url'], 'title' => "上传图片",'url'=>[$slide['url']]]) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="1" type="radio" class="iradio" @if($slide['is_show']==1) checked="checked"@endif/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="0" type="radio" class="iradio" @if($slide['is_show']==0) checked="checked"@endif/> 否</label>
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
