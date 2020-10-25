@section('title', '更改手机号第一步 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column">
                        <div class="articleInfo">
                            <h4 class="ui header">更换手机号</h4>
                            <div class="changePhone">
                                <div class="ui ordered steps">
                                    <div class="step">
                                        <h5 class="ui icon header">
                                            <img src="{{asset('front/image/changePhone3.png')}}">
                                            <div class="content">请先验证当前手机号</div>
                                        </h5>
                                    </div>
                                    <div class="step">
                                        <h5 class="ui icon header">
                                            <img src="{{asset('front/image/changePhone2.png')}}">
                                            <div class="content">绑定新手机号</div>
                                        </h5>
                                    </div>
                                </div>
                                <div class="changePhoneForm">
                                    <p class="msg_error"></p>
                                    <form class="ui form" id="changePhoneOneForm" method="post">
                                        {{csrf_field()}}
                                        <textarea id="key" style="display: none">{!! $key !!}</textarea>
                                        <div class="field">
                                            <input type="text" name="changePhoneOnePhone" id="changePhoneOnePhone" placeholder="当前手机号">
                                        </div>
                                        <div class="field">
                                            <div class="fields">
                                                <div class="nine wide field">
                                                    <input type="text" name="changePhoneOneCaptcha" id="changePhoneOneCaptcha" placeholder="图文验证码">
                                                </div>
                                                <div class="seven wide field">
                                                    <img id="captcha_img" src="{{captcha_src()}}" onclick="changeCaptcha()">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="fields">
                                                <div class="nine wide field">
                                                    <input type="text" name="changePhoneOneCode" id="changePhoneOneCode" placeholder="输入验证码">
                                                </div>
                                                <div class="seven wide field">
                                                    <input type="button" id="getVerificationCode" class="ui button primary" value="获取验证码">
                                                </div>
                                            </div>
                                            <p class="verificationCodeReminder">验证码已经发送至您的手机，如没有收到，请在倒计时结束后<a style="text-decoration: none">重新发送</a></p>
                                        </div>
                                        <button class="ui button formBtn next" type="submit">下一步</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
<script>
    function changeCaptcha() {
        $.get('/captcha', function (res) {
            $('#captcha_img').attr('src', res.data+'?v='+Math.random());
        });
    }
    $(function(){
        $('#getVerificationCode').on('click', function () {
            var mobile = $('#changePhoneOnePhone').val();
            var captcha = $('#changePhoneOneCaptcha').val();
            var obj = $(this);
            if(!mobile){
                msg_error('请输入正确的手机号', 'changePhoneOnePhone');
                ;return;}
            if(!captcha){
                msg_error('请输入图形验证码', 'changePhoneOneCaptcha');
                return;
            }
            $.get('/sendSmsNoAuth?phone='+mobile+'&captcha='+captcha, function (res) {
                if(res.code != 200) {
                    changeCaptcha();
                    msg_error(res.data);
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
        $('form').on('submit', function () {
            if(!$('#changePhoneOnePhone').val()) {
                msg_error('请输入手机号', 'changePhoneOnePhone');
                return false;
            }
            if(!$('#changePhoneOneCaptcha').val()) {
                msg_error('请输入图文验证码', 'changePhoneOneCaptcha');
                return false;
            }
            if(!$('#changePhoneOneCode').val()) {
                msg_error('请输入短信验证码', 'changePhoneOneCode');
                return false;
            }
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#changePhoneOneCode').val();
            var enc = crypt.encrypt(old);
            $('#changePhoneOneCode').val(enc);
            var form = $('.form').serialize();
            $('#changePhoneOneCode').val(old);
            $.ajax({
                type : "post",
                url : "/member/editOneChangePhone",
                data : form,
                async : false,
                success : function(res){
                    if(res.code != 200) {
                        changeCaptcha();
                        $('#changePhoneOneCode').val(old);
                        msg_error(res.data);
                        return false;
                    } else {
                        location.href = '/member/twoChangePhone';
                    }
                }
            });

            return false;
        })
    })
</script>
