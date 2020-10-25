@extends('layouts.admin')

@section('title', '新增拍品')
@section('content')
    <style>
        .auction, .guess{
            display: none;
        }
    </style>
    <div class="page-title">
        <h2><a href="{{route('tender')}}"><i class="fa fa-reply"></i></a> 新增拍品</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增项目
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('tender/create')}}" role="form" method="post" novalidate enctype="multipart/form-data">
                    {{csrf_field()}}
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => old('name'), 'placeholder' => "请输入项目名称", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '楼盘代码', 'name' => 'code', 'value' => old('code'), 'placeholder' => "请输入楼盘代码", 'ext'=>'required']) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">类型</label>
                        <div class="col-md-10">
                            <select name="type" class="form-control">
                                <option value="" selected="">类型</option>
                                <option value="0">暗标</option>
                                <option value="1">竞拍</option>
                            </select>
                        </div>
                    </div>

                    {!! Form::bsText(['label' => '估值', 'name' => 'valuation', 'value' => old('valuation'), 'placeholder' => "请输入楼盘估值", 'ext'=>'required']) !!}

                    <div class="form-group auction">
                        <label class="col-md-2 control-label">起拍价</label>
                        <div class="col-md-10">
                            <input type="number" name="starting_price" class="form-control" step="0.01" min="0.01" value="0.00" placeholder="请输入起拍价">
                        </div>
                    </div>

                    {!! Form::bsText(['label' => '介绍视频', 'name' => 'video', 'value' => old('video'), 'placeholder' => "请输入视频地址", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['label' => '视频截图', 'name' => 'poster', 'id' => 'filename1','value'=>old('poster'), 'title' => "poster",'url'=>[]]) !!}
                    {!! Form::bsFile(['label' => 'banner', 'name' => 'banner', 'id' => 'filename2','value'=>old('banner'), 'title' => "banner",'url'=>[]]) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">产品介绍:</label>
                        <div class="col-md-10">
                            <script id="container" name="info" type="text/plain"></script>
                        </div>
                    </div>

                    <div class="form-group guess">
                        <label class="col-md-2 control-label">估价开始时间</label>
                        <div class="col-md-5">
                            <input id="d4311" required class="wdate form-control" type="text" name="guess_start" value="{{old('guess_start')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="开始时间">
                            {{--<input type="text" name="start" required class="form-control datepicker" value="{{old('start')}}">--}}
                        </div>
                    </div>
                    <div class="form-group guess">
                        <label class="col-md-2 control-label">估价结束时间</label>
                        <div class="col-md-5">
                            <input id="d4311" required class="wdate form-control" type="text" name="guess_end" value="{{old('guess_end')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="结束时间">
                            {{--<input type="text" name="end" required class="form-control datepicker" value="{{old('end')}}">--}}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-2 control-label">投标开始时间</label>
                        <div class="col-md-5">
                            <input id="d4311" required class="wdate form-control" type="text" name="tender_start" value="{{old('tender_start')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="开始时间">
                            {{--<input type="text" name="start" required class="form-control datepicker" value="{{old('start')}}">--}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">投标结束时间</label>
                        <div class="col-md-5">
                            <input id="d4311" required class="wdate form-control" type="text" name="tender_end" value="{{old('tender_end')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="结束时间">
                            {{--<input type="text" name="end" required class="form-control datepicker" value="{{old('end')}}">--}}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="stat" value="0" type="radio" class="iradio"/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="stat" value="-1" type="radio" class="iradio" checked="checked"/> 否</label>
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
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.initialFrameHeight = 1000;
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });
    $(function () {
        $('select[name=type]').on('change', function () {
            var type = $(this).val()
            if(type === '0') {
                $('.guess').show();
                $('.auction').hide();
            } else if(type === '1'){
                $('.guess').hide();
                $('.auction').show();
            } else {
                $('.guess').hide();
                $('.auction').hide();
            }
        })
    })
</script>
@endpush
