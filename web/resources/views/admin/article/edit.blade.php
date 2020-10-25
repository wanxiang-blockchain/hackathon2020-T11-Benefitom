@extends('layouts.admin')

@section('title', '新增文章管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('project')}}"><i class="fa fa-reply"></i></a> 编辑文章</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                编辑文章
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('article/postEdit')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$article['id']}}">
                    {!! Form::bsText(['label' => '标题', 'name' => 'title', 'value' => $article['title'], 'placeholder' => "请输入文章标题名称", 'ext'=>'required']) !!}
                    @if(isset($article['pictures']['0']))
                        {!! Form::bsFile(['name' => 'picture', 'id' => 'filename2','value'=>$article['pictures'][0]['url'], 'title' => "上传图片",'url'=>[$article['pictures'][0]['url']]])!!}
                    @else
                        {!! Form::bsFile(['name' => 'picture', 'id' => 'filename2','value'=>"", 'title' => "上传图片",'url'=>[]])!!}
                    @endif
                    <div class="form-group">
                        <label class="col-md-2 control-label">是否展示</label>
                        <div class="col-md-4">
                            <label class="check"><input type="radio" value="1" class="iradio" name="is_show" @if($article['is_show']==1) checked="checked"@endif/> 是</label>
                            <label class="check"><input type="radio" value="0" class="iradio" name="is_show" @if($article['is_show']==0) checked="checked"@endif/> 否</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">详情:</label>
                        <div class="col-md-10">
                            <script id="container" name="content" type="text/plain">{!! $article['content'] !!}</script>
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
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.initialFrameHeight = 1000;
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });
</script>
@endpush
