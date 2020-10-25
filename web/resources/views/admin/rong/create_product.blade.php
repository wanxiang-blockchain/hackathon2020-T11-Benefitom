@extends('layouts.admin')
@section('title', '新增产品')
@section('content')
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4>新增产品</h4>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('rong/product/create')}}" role="form" method="post" enctype="multipart/form-data"enctype="multipart/form-data">
                    {!! Form::token() !!}
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => old('name'), 'placeholder' => "请输入产品名称"]) !!}
                    {!! Form::bsNumber(['label' => '单价', 'name' => 'price', 'value' => old('price'), 'placeholder' => "请输入单价",'ext'=>'required step=0.01 min=0.01']) !!}
                    {!! Form::bsNumber(['label' => '期限(以月为单位)', 'name' => 'duration', 'value' => old('duration'), 'placeholder' => "请输入期限",'ext'=>'required step=1 min=1']) !!}
                    {!! Form::bsNumber(['label' => '利率', 'name' => 'rate', 'value' => old('rate'), 'placeholder' => "请输入年利率",'ext'=>'required step=0.01 min=0.01']) !!}
{{--                    {!! Form::bsText(['label' => '发行数量', 'name' => 'amount', 'value' => old('amount'), 'placeholder' => "请输入发行数量", 'ext'=>'required step=1 min=1']) !!}--}}
                    {!! Form::bsFile(['label' => '上传缩略图', 'name' => 'picture', 'id' => 'filename2','value'=>old('picture'), 'title' => "上传缩略图",'ext'=>'required','url'=>[]]) !!}
                    {!! Form::bsFile(['label' => '上传banner图', 'name' => 'banner', 'id' => 'filename3','value'=>old('banner'), 'title' => "上传banner",'ext'=>'required','url'=>[]]) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">产品介绍:</label>
                        <div class="col-md-10">
                            <script id="container" name="info" type="text/plain"></script>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="enable" value="1" type="radio" class="iradio"/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="enable" value="0" type="radio" class="iradio" checked="checked"/> 否</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-10">
                            <input type="button" id="submit" class="btn btn-danger btn-lg mb-control" value="提交">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.initialFrameHeight = 1000;
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });




    $(function () {
        $('#submit').on('click', function () {

            var form = new FormData(document.querySelector('form'))

            $.ajax({
                url: '{{route('rong/product/create')}}',
                data: form,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(res){
                    if(res.code != 200 ) {
                        swal('', res.data, 'error');
                        return false;
                    } else {
                        swal('', res.data, 'success');
                        window.location.href = '/admin/rong?nav=9|1';
                    }
                }
            });

        });
    })
</script>
@endpush