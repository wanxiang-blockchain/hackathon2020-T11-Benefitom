@section('title', '注册')
@include("front.layouts.head")
<style>
    .go_reg{
        display: none !important;
    }
</style>
<div class="pusher login">
    <div class="ui middle stackable grid container">
        <div class="ts_login row">
            <div class="ten wide column">
                <img class="ui rounded image" src="{{asset('front/image/logo.png')}}">
            </div>
            <div class="six wide column loginForm">
                <h3>益通云注册</h3>
                <p class="msg_error"><b></b></p>
                <form class="ui form" id="registerForm" method="post" action="{{route('postRegister')}}">
                    {{csrf_field()}}
                    <textarea id="key" style="display: none">{!! $key !!}</textarea>
                    <div class="field">
                        <input type="number" id="phone" name="phone" maxlength="11" placeholder="请输入您的手机号" >
                    </div>

                    <div class="field">
                        <div class="fields">
                            <div class="eleven wide field">
                                <input type="text" id="verificationCode" name="verificationCode" placeholder="请输入验证码">
                            </div>
                            <div class="five wide field">
                                <input type="button" id="getVerificationCode" class="ui button primary" value="获取验证码">
                            </div>
                        </div>
                        <p class="verificationCodeReminder">验证码已经发送至您的手机，如没有收到，请在倒计时结束后<a style="text-decoration: none">重新发送</a></p>
                    </div>
                    <div class="field">
                        <input type="password" id="password" name="password" placeholder="请设置登录密码">
                    </div>
                    <p class="tradePasswordText">为保障您的账户认购安全与快捷，请您务必设置交易密码！</p>
                    <div class="field">
                      <input type="password" id="tradePassword" name="tradePassword" placeholder="请设置交易密码">
                    </div>
                    <div class="field">
                      <input type="password" id="againTradePassword" name="againTradePassword" placeholder="再次输入交易密码">
                    </div>
                    <div class="fields">
                        <div class="five wide field">
                            <input type="button" class="ui button primary" value="邀请码">
                        </div>
                        <div class="eleven wide field">
                            <input type="text" id="invite_member" name="invite_member" value="{{$invite_member}}">
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui checkbox">
                            <input type="checkbox" tabindex="0" class="hidden" name="agreement" id="agreement">
                            <label>我已阅读并同意<a href="javascript:;" target="_blank">《北京益通云文化有限公司艺术收楼盘数字化认购与交易协议》</a></label>
                        </div>
                    </div>
                    <button class="ui button formBtn finish" type="button">完成注册</button>
                </form>
                <p class="go_login">已有账户？<a href="{{route('login')}}">立即登录</a></p>
            </div>
        </div>
    </div>
</div>

@include("front.layouts.foot")
<script>
    function changeCaptcha() {
        $.get('captcha', function (res) {
            $('#captcha_img').attr('src', res.data+'?v='+Math.random());
        });
    }


    $(function(){
        $('#getVerificationCode,#resend').on('click', function () {
            var mobile = $('#phone').val();
            var captcha = $('#captcha').val();
            var obj = $(this);
            if(!mobile){
                msg_error('请输入正确的手机号', 'phone');
                return false;
            }
            if(!captcha){
    //            msg_error('请输入图文验证码', 'captcha');
     //           return false;
            }
            $("body").ajaxError(function(){
                alert("An error occurred!");
            });
            $.get('sendSms?phone='+mobile+'&captcha='+captcha, function (res) {
                if(res.code != 200) {
                    changeCaptcha();
                    /*if(res.data == '请输入正确的图文验证码') {

                    }*/
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
                                    changeCaptcha();
                                    obj.removeClass('grey').addClass(
                                        'primary');
                                    clearInterval(ts);
                                    obj.removeAttr('disabled');
                                }
                            }, 1000)
                    }
                    msg_error(res.data);
                    return false;
                } else {
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
            });

        });
        $('.finish').on('click', function(){
            if(!$('#phone').val()) {
                msg_error('请输入手机号', 'phone');
                return false;
            }
            if(!$('#captcha').val()) {
            //    msg_error('请输入图文验证码', 'captcha');
             //   return false;
            }
            if(!$('#getVerificationCode').val()) {
                msg_error('请输入短信验证码', 'getVerificationCode');
                return false;
            }
            if(!$('#password').val()) {
                msg_error('请输入密码', 'password');
                return false;
            }
            if(!$('#tradePassword').val()) {
                msg_error('请输入交易密码', 'tradePassword');
                return false;
            }
            if(!$('#againTradePassword').val()) {
                msg_error('请再次输入交易密码', 'againTradePassword');
                return false;
            }
            if($('#againTradePassword').val() != $('#tradePassword').val()) {
                msg_error('两次输入交易密码不一致', 'againTradePassword');
                return false;
            }
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#password').val();
            var enc = crypt.encrypt(old);
            var verificationCode = $('#verificationCode').val();
            var verify = crypt.encrypt(verificationCode);
            $('#password').val(enc);
            $('#verificationCode').val(verify);
            var tradePassword = $('#tradePassword').val();
            var tradePassword_mi = crypt.encrypt(tradePassword);
            $('#tradePassword').val(tradePassword_mi);
            var againTradePassword = $('#againTradePassword').val();
            var againTradePassword_mi = crypt.encrypt(againTradePassword);
            $('#againTradePassword').val(againTradePassword_mi);
            var form = $('form').serialize();
            $('#verificationCode').val(verificationCode);
            $('#tradePassword').val(tradePassword);
            $('#againTradePassword').val(againTradePassword);
            $('#password').val(old);
            $.post('register', form, function(res){
                if(res.code != 200) {
                    changeCaptcha();
                    /*if(res.data == '请输入正确的图文验证码') {
                        changeCaptcha();
                    }*/
                    $('#verificationCode').val(verificationCode);
                    $('#password').val(old);
                    msg_error(res.data);
                } else {
                    setTimeout(function(){
                        location.href = '/login';
                    }, 1000);
                }
            });
            $('#verificationCode').val(verificationCode);
        })
    })
</script>
