@extends('layouts.admin')

@section('title', '新增项目管理')
@section('content')
    <div class="col-md-8">
        @include('layouts.error')
        <h4>新增管理员</h4>
        <form class="form-horizontal" action="{{route('manage/create')}}" role="form" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <textarea id="key" style="display: none">{!! $key !!}</textarea>
            {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => old('name'), 'placeholder' => "请输入管理员名称"]) !!}
            {!! Form::bsText(['label' => '密码', 'name' => 'password', 'value' => "", 'placeholder' => "请设置登录密码"]) !!}
            {!! Form::bsText(['label' => '手机号', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入手机号"]) !!}
            {!! Form::bsSelect("角色", "role_type", $values) !!}


            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-10">
                    <input type="button" id="submit" class="btn btn-danger" value="提交">
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
    <script>
        $('#submit').on('click', function () {
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('input[name="password"]').val();
            var enc = crypt.encrypt(old);
            $('input[name="password"]').val(enc);
            var form = $('form').serialize();
            $('input[name="password"]').val(old);
            $.post('{{route('manage/create')}}', form, function (res) {
                if(res.code != 200 ) {
                    swal('', res.data, 'error');
                    return false;
                } else {
                    swal('', res.data, 'success');
                    setTimeout(function () {
                        window.location.href = '/admin/manage/user?nav=8|2';
                    }, 1000);
                }
            });
        });
    </script>
@endpush
