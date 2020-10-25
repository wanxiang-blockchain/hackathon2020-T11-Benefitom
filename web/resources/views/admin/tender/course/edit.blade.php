@extends('layouts.admin')

@section('title', '新增课程')
@section('content')

    <div class="page-title">
        <h2><a href="{{route('tender')}}"><i class="fa fa-reply"></i></a> 新增课程</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增课程
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('tender/course/create')}}" role="form" method="post" novalidate enctype="multipart/form-data">
                    {{csrf_field()}}
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => $model['name'], 'placeholder' => "请输入课程名称", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '课程摘要', 'name' => 'summary', 'value' => $model['summary'], 'placeholder' => "请输入课程摘要", 'ext'=>'required']) !!}

                    {!! Form::bsText(['label' => '课程视频', 'name' => 'video', 'value' => $model['video'], 'placeholder' => "请输入视频地址", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '视频截图', 'name' => 'poster', 'value'=>$model['poster'], 'placeholder' => '请输入视频截图地址', 'ext' => 'required']) !!}
                    <input name="id" type="hidden" value="{{$model->id}}">

                    <div class="form-group">
                        <label class="col-md-2 control-label">产品介绍:</label>
                        <div class="col-md-10">
                            <script id="container" name="info" type="text/plain">{!! $model->info !!}</script>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">是否发布</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input @if($model->stat == 1) checked="checked" @endif name="stat" value="1" type="radio" class="iradio"/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input @if($model->stat == 0) checked="checked" @endif  name="stat" value="0" type="radio" class="iradio" /> 否</label>
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
<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.initialFrameHeight = 1000;
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });
    $(function () {

    })
</script>
@endpush
