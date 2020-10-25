@extends('layouts.admin')

@section('title', '新增项目管理')
@section('content')
    <div class="col-md-8">
        @include('layouts.error')
        <h4>修改管理员</h4>
        <form class="form-horizontal" action="{{route('manage/postEdit')}}" role="form" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <textarea id="key" style="display: none">{!! $key !!}</textarea>
            <input type="hidden" name="id" value="{{$user['id']}}"/>
            {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => "{$user['name']}", 'placeholder' => "请输入管理员名称"]) !!}
            {!! Form::bsText(['label' => '密码', 'name' => 'password', 'value' => "", 'placeholder' => "请输入密码,不填默认不修改"]) !!}
            {!! Form::bsText(['label' => '手机号', 'name' => 'phone', 'value' => "{$user['phone']}", 'placeholder' => "请输入手机号"]) !!}
            @if(\Auth::user()->id != $user['id'])
            {!! Form::bsSelect("角色", "role_type", $values) !!}
            @endif

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
        $.post('{{route('manage/postEdit')}}', form, function (res) {
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
