@extends('layouts.admin')
@section('title', '编辑会员')
@section('content')
    <div class="col-md-8">
        <h4>编辑会员</h4>
        <form class="form-horizontal" action="{{route('member/postEdit')}}" role="form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{$member['id']}}"/>
            {!! Form::token() !!}
            <textarea id="key" style="display: none">{!! $key !!}</textarea>
            {!! Form::bsText(['label' => '真实姓名', 'name' => 'name', 'value' => $member['name'], 'placeholder' => "请输入真实姓名"]) !!}
            {!! Form::bsText(['label' => '昵称 ', 'name' => 'nickname', 'value' => $member['nickname'], 'placeholder' => "请输入昵称"]) !!}
            {!! Form::bsText(['label' => '手机号 ', 'name' => 'phone', 'value' => $member['phone'], 'placeholder' => "请输入联系方式"]) !!}
            {!! Form::bsText(['label' => '登录密码 ', 'name' => 'password', 'value' => "", 'placeholder' => "为空则不做修改"]) !!}
            {!! Form::bsText(['label' => '身份证号码 ', 'name' => 'code', 'value' => $member['code'], 'placeholder' => "请输入身份证号码"]) !!}
            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-10">
                    <input type="button" id="submit" class="btn btn-danger btn-lg mb-control" value="提交">
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
<script>
    $('#submit').on('click', function () {
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var old = $('input[name="password"]').val();
        var agin = $('input[name="trade_pwd"]').val();
        var code = $('input[name="code"]').val();
        var enc = crypt.encrypt(old);
        var ag = crypt.encrypt(agin);
        var c = crypt.encrypt(code);
        $('input[name="password"]').val(enc);
        $('input[name="trade_pwd"]').val(ag);
        $('input[name="code"]').val(c);
        var form = $('form').serialize();
        $('input[name="password"]').val(old);
        $('input[name="trade_pwd"]').val(agin);
        $('input[name="code"]').val(code);
        $.post('{{route('member/postEdit')}}', form, function (res) {
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                return false;
            } else {
                swal('', res.data, 'success');
                setTimeout(function () {
                    window.location.href = '/admin/member?nav=6|1';
                }, 1000);
            }
        });
    });
</script>
@endpush