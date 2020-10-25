@extends('layouts.admin')

@section('title', '充值')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('finance/recharge')}}"><i class="fa fa-reply"></i></a> 管理员充值</h2>
    </div>
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                管理员充值
            </div>
            <div class="panel-body">
                <form class="form-horizontal" id="form" action="{{route('finance/addRecharge')}}" role="form" method="post">
                    {{csrf_field()}}
                    <textarea id="key" style="display: none">{!! $key !!}</textarea>
                    {!! Form::bsText(['label' => '用户手机号', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入用户手机号", 'ext'=>'required']) !!}
                    {!! Form::bsSelect("充值资产", "asset_type", $values) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">解锁时间</label>
                        <div class="col-md-5">
                            <input required class="wdate form-control" type="text" name="time" value=""
                                   onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="解锁时间">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">充值金额</label>
                        <div class="col-md-10">
                            <input type="text" name="price" min="1" step="0.01" class="form-control money"  required   placeholder="请输入充值金额" value="{{old('price')}}" />
                        </div>
                    </div>
                    <div class="form-group dan" style="display: none">
                        <label class="col-md-2 control-label">单价</label>
                        <div class="col-md-10">
                            <input type="text" name="balance" min="1" step="0.01" class="form-control"  required   placeholder="请输入单价" value="{{old('balance')}}" />
                        </div>
                    </div>
                    {{--{!! Form::bsText(['label' => '充值金额', 'name' => 'price', 'value' => old('price'), 'placeholder' => "请输入充值金额", 'ext'=>'required']) !!}--}}
                    <div class="form-group">
                        <label class="col-md-2 control-label">管理员密码</label>
                        <div class="col-md-10">
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
                <div class="mb-title"><span class="fa fa-warning"></span> 确认充值?</div>
                <div class="mb-content">
                    <h3 style="color: #fff">
                        <p>充值用户: <strong>12121212121</strong></p>
                        <p>充值资产: <strong>12121212121</strong></p>
                        <p>解锁时间: <strong>12121212121</strong></p>
                        <p>解锁金额: <strong>12121212121</strong></p>
                        <p>单价: <strong>12121212121</strong></p>
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
    $('#submit').on('click',function(){
       $('.btn').attr('disabled', true);
    });
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
        $.post('{{route('finance/addRecharge')}}', form, function (res) {
            console.log(res);
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                $('.btn').attr('disabled',false);
                return false;
            } else {
                swal('', res.data, 'success');
//                setTimeout(function () {
//                    window.location.reload();
//                }, 1000);
                location.href='/admin/finance/audit_list/?nav=4|6';
            }
        });
    });
    $('#submit').on('click', function (e) {
        e.stopPropagation();
        var asset_type = $('select').val();
        var money = $('input[name=price]').val();
        if(asset_type == 'T000000001') {
            if(!/^(-?\d+)(\.\d+)?$/.test(money)) {
                $('.btn').attr('disabled', false);
                swal('', '请输入正确的金额', 'error');
                return false;
            }
        } else {
            if(!/^[0-9]*[1-9][0-9]*$/.test(money)) {
                $('.btn').attr('disabled', false);
                swal('', '请输入正确的整数', 'error');
                return false;
            }
        }
        if(asset_type =='T000000001' ){
            $('.mb-content').html('<h3 style="color: #fff"><p style="padding: 8px">充值用户: <strong>'+$('input[name=phone]').val()+'</strong></p><p style="padding: 8px">充值资产: <strong>'+$('select[name=asset_type] option:selected').text()+'</strong></p> ' +
                '<p style="padding: 8px">解锁时间: <strong>'+$('input[name=time]').val()+'</strong></p><p style="padding: 8px">充值金额: <strong>'+$('input[name=price]').val()+'</strong></p> <p style="padding: 8px"></p> </h3>');
        }else{
            $('.mb-content').html('<h3 style="color: #fff"><p style="padding: 8px">充值用户: <strong>'+$('input[name=phone]').val()+'</strong></p><p style="padding: 8px">充值资产: <strong>'+$('select[name=asset_type] option:selected').text()+'</strong></p> ' +
                '<p style="padding: 8px">解锁时间: <strong>'+$('input[name=time]').val()+'</strong></p><p style="padding: 8px">充值金额: <strong>'+$('input[name=price]').val()+'</strong></p> <p style="padding: 8px">单价: <strong>'+$('input[name=balance]').val()+'</strong></p> </h3>');
        }
        $('.btn').attr('disabled', false);
        $("#message-box-warning").toggleClass("open");
    });

   // $('.money').on('change', isOk(/^(-?\d+)(\.\d+)?$/, "必须为数字"));
    $('.select').on('change', function () {
        var val = $(this).val();
        if(val != 'T000000001') {
            $('input[name="price"]').parent().parent().find('label').html('充值份数');
            $('.money').attr('placeholder','请输入份数');
            $('.money').on('change', isOk(/^-?\d+$/, "份数必须为整数"));
            $('.dan').show();
        } else {
            $('input[name="price"]').parent().parent().find('label').html('充值金额');
            $('.money').attr('placeholder','请输入金额');
            $('.money').on('change', isOk(/^(-?\d+)(\.\d+)?$/, "金额必须为数字"));
            $('.dan').hide();
        }
    });


    function isOk(rule, info) {
        return function() {
            var value = $(this).val();
            if (rule.test(value)) {
                ;
            } else{
                //alert(info);
                return false;
            }
        }
    }
</script>
@endpush
