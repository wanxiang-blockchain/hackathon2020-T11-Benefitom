@section('title', '找回密码第二步 - 用户管理中心')
@include("front.layouts.head")
<script type="text/javascript" src="js/jsencrypt.min.js"></script>
<div class="pusher login">
    <div class="ui container certification_common">
        <div class="ts_certivication">
            <div class="resetPassword">
                <div class="ui ordered steps">
                    <div class="active step">
                        <h5 class="ui icon header">
                            <img src="{{asset('front/image/step_active1.png')}}">
                            <div class="content">填写手机号</div>
                        </h5>
                    </div>
                    <div class="step">
                        <h5 class="ui icon header">
                            <img src="{{asset('front/image/step_active2.png')}}">
                            <div class="content">重置密码</div>
                        </h5>
                    </div>
                    <div class="step">
                        <h5 class="ui icon header">
                            <img src="{{asset('front/image/step5.png')}}">
                            <div class="content">完成</div>
                        </h5>
                    </div>
                </div>
                {{-- <p class="textMessage">找回密码需要短信确认，验证码已发送到手机{!! substr_replace($phone,'*****', 3,5) !!}，请按提示操作</p>--}}
                <div class="resetPasswordForm">
                    <p class="msg_error"></p>
                    <form class="ui form" id="resetPasswordTwo">
                        {{csrf_field()}}
                        <textarea id="key" style="display: none">{!! $key !!}</textarea>
                        <input type="hidden" name="phone" value="{{$phone}}">
                        <div class="field">
                            <div class="fields">
                                <div class="eleven wide field">
                                    <input type="text" name="verificationCode" id="resetPwTwoCaptcha" placeholder="请输入短信验证码">
                                </div>
                                <div class="five wide field">
                                    <input type="button" id="getVerificationCode" data-phone = "{{$phone}}" class="ui button primary" value="获取验证码">
                                </div>
                            </div>
                            <p class="verificationCodeReminder">验证码已经发送至您的手机，如没有收到，请在倒计时结束后<a style="text-decoration: none">重新发送</a></p>
                        </div>
                        <div class="field">
                            <input type="password" name="password" id="newPassword" placeholder="新密码6-20位密码">
                        </div>
                        <div class="field">
                            <input type="password" name="password_confirmation" id="againPassword" placeholder="确认新密码">
                        </div>
                        <button class="ui button formBtn next" type="button">下一步</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
<script>
    $(function(){
        $('#getVerificationCode').on('click', function () {
            var mobile = $(this).data('phone');
            var obj = $(this);
            $.get('sendSmsNoAuth?phone='+mobile, function (res) {
                if(res.code != 200) {
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
        $('.next').on('click', function(){
            if(!$('#resetPwTwoCaptcha').val()) {
                msg_error('请输入验证码', 'resetPwTwoCaptcha');
                return false;
            }
            if(!$('#newPassword').val()) {
                msg_error('请输入密码', 'newPassword');
                return false;
            }
            if(!$('#againPassword').val()) {
                msg_error('请输入重复密码', 'againPassword');
                return false;
            }
            if($('#newPassword').val() !=  $('#againPassword').val()) {
                msg_error('密码不一致', 'againPassword');
                return false;
            }
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#newPassword').val();
            var again = $('#againPassword').val();
            var verificationCode = $('#resetPwTwoCaptcha').val();
            var enc = crypt.encrypt(old);
            var ag = crypt.encrypt(again);
            var verify = crypt.encrypt(verificationCode);
            $('#newPassword').val(enc);
            $('#againPassword').val(ag);
            $('#resetPwTwoCaptcha').val(verify);
            var form = $('form').serialize();
            $('#newPassword').val(old);
            $('#againPassword').val(again);
            $('#resetPwTwoCaptcha').val(verificationCode);
            $.post('twoForget', form, function(res){
                if(res.code != 200) {
                    msg_error(res.data);
                } else {
                    location.href = '/getThreeForget';
                }
            });
        })
    })
</script>
