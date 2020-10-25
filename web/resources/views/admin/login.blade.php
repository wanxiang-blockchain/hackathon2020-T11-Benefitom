<!DOCTYPE html>
<html lang="en" class="body-full-height">
<head>
    <!-- META SECTION -->
    <title>后台登录系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{--<link rel="icon" href="favicon.ico" type="image/x-icon" />--}}
    <link rel="shortcut icon" href="{{'/front/favicon.ico'}}">
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('css/admin')}}/theme-default.css"/>
    <script type="text/javascript" src="/js/jsencrypt.min.js"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/jquery/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('js/admin')}}/plugins/sweetalert/sweetalert.css"/>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/sweetalert/sweetalert.min.js"></script>

    <!-- EOF CSS INCLUDE -->
</head>
<body>

<div class="login-container">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="login-box animated fadeInDown">
        <div class="login-logo"></div>
        <div class="login-body">
            <div class="login-title"><strong>欢迎您</strong>, 请登录</div>
            <form class="form-horizontal">
                {{ csrf_field() }}
                <textarea id="key" style="display: none">{!! $key !!}</textarea>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="text" name="phone" id="phone" value="" class="form-control" placeholder="请输入手机号"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-sm-8" style="padding: 0px;">
                            <input type="text" class="form-control" id="verificationCode" name="verificationCode" placeholder="请输入验证码">
                        </div>
                        <div class="col-sm-2">
                            <input type="button" style="padding: 8px;" id="getVerificationCode" class="btn btn-primary" value="获取验证码">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="password" name="password" id="password" class="form-control" placeholder="请输入密码"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <button class="btn btn-info btn-block" id="submit">登 录</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="login-footer">
            <div class="pull-left">
                &copy; 2020 益通云
            </div>

        </div>
    </div>

</div>
<script>
    $(function(){
        $('#submit').on('click', function(){
            if(!$('#phone').val())
            {
                swal('请输入正确的手机号');
                return false;
            }

            if(!$('#password').val())
            {
                swal('请输入密码');
                return false;
            }

            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#password').val();
            var enc = crypt.encrypt(old);
            $('#password').val(enc);
            var form = $('form').serialize();
            $('#password').val(old);
            $.post('/admin/login', form, function(res){
                if(res.code != 200) {
                    swal(res.data);
                } else {
                    setTimeout(function(){
                        location.href = '/admin/hello?nav=1';
                    }, 1000);
                }
            }, 'json');
            return false;
        })

        //短信验证
        $('#getVerificationCode,#resend').on('click', function () {
            var mobile = $('#phone').val();
            var obj = $(this);
            if(!mobile){
                swal('请输入正确的手机号');
                return false;
            }
            $("body").ajaxError(function(){
                alert("An error occurred!");
            });
            obj.attr('disabled', true);
            $.get('/sendSmsNoAuth?phone='+mobile, function (res) {
                if(res.code != 200) {
                    if(res.code == 203) {
                        obj.attr('disabled', true);
                        obj.removeClass('primary').addClass('grey');
                        obj.parent().parent().parent().find($(".verificationCodeReminder"))
                            .css({
                                "display": "block"
                            });
                        var max = res._data,
                            ts = setInterval(function () {
                                obj.val('已发送(' + (max--) + ')s');
                                if (max < 0) {
                                    obj.attr('disabled', false);
                                    obj.val('重新获取');
                                    obj.removeClass('grey').addClass(
                                        'primary');
                                    clearInterval(ts);
                                    obj.removeAttr('disabled');
                                }
                            }, 1000)
                    }
                    swal(res.data);
                    obj.attr('disabled', true);
                    obj.removeClass('primary').addClass('grey');
                    obj.parent().parent().parent().find($(".verificationCodeReminder"))
                        .css({
                            "display": "block"
                        });
                    var max = res.data,
                        ts = setInterval(function () {
                            obj.val('已发送(' + (max--) + ')s');
                            if (max < 0) {
                                obj.attr('disabled', false);
                                obj.val('重新获取');
                                obj.removeClass('grey').addClass(
                                    'primary');
                                clearInterval(ts);
                                obj.removeAttr('disabled');
                            }
                        }, 1000)
                }
                obj.attr('disabled', false);
            });

        });

    })
</script>

</body>
</html>

