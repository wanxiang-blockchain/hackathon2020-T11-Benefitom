@extends('layouts.admin')

@section('title', '提现')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('withdrawAudit/add')}}"><i class="fa fa-reply"></i></a> 管理员提现</h2>
    </div>
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                管理员提现
            </div>
            <div class="panel-body">
                <form class="form-horizontal" id="form" action="{{route('withdrawAudit/add')}}" role="form" method="post">
                    {{csrf_field()}}
                    <textarea id="key" style="display: none">{!! $key !!}</textarea>
                    {!! Form::bsText(['label' => '用户手机号', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入用户手机号", 'ext'=>'required']) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">提现金额</label>
                        <div class="col-md-10">
                            <input type="text" name="amount" class="form-control money"  required   placeholder="请输入提现金额" value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">管理员密码</label>
                        <div class="col-md-10">
                            <input type="password" name="password1" id="password1" style="position: absolute; top: -1000px;" />
                            <input type="password" name="password" id="password" class="form-control"  value="{{old('password')}}"  placeholder="请输入密码"/>
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
    <div class="message-box message-box-warning animated fadeIn" id="message-box-warning">
        <div class="mb-container">
            <div class="mb-middle">
                <div class="mb-title"><span class="fa fa-warning"></span> 确认提现?</div>
                <div class="mb-content">
                    <h3 style="color: #fff">
                        <p>提现用户: <strong id="show_phone">12121212121</strong></p>
                        <p>提现金额: <strong id="show_amount">12121212121</strong></p>
                    </h3>
                </div>
                <div class="mb-footer">
                    <button class="btn btn-danger btn-lg pull-right mb-control-close" id="form_submit">确定</button>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 8px">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>

    $('#form_submit').on('click', function () {
        $('.btn').attr('disabled',true);
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var old = $('#password').val();
        var enc = crypt.encrypt(old);
        $('#password').val(enc);
        var form = $('form').serialize();
        $('#password').val(old);
        $.post('{{route('withdrawAudit/add')}}', form, function (res) {
            console.log(res);
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                $('.btn').attr('disabled',false);
                return false;
            } else {
                swal('', res.data, 'success');
                location.href='/admin/withdrawAudit/?nav=4|9';
            }
        });
    });

    $('#submit').on('click', function (e) {
        e.stopPropagation();
        $('.btn').attr('disabled', true);
        var amount = $('input[name=amount]').val();
        var phone = $('input[name=phone]').val();

        if(!/^(-?\d+)(\.\d+)?$/.test(amount)) {
            $('.btn').attr('disabled', false);
            swal('', '请输入正确的金额', 'error');
            return false;
        }

        if(phone == '') {
            $('.btn').attr('disabled', false);
            swal('', '请填写完事用户信息', 'error');
            return false;
        }

        $('#show_phone').text(phone)
        $('#show_amount').text(amount)

        $('.btn').attr('disabled', false);
        $("#message-box-warning").toggleClass("open");
    });

</script>
@endpush
