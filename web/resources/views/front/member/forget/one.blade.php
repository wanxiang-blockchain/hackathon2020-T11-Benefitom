@section('title', '找回密码第一步 - 用户管理中心')
@include("front.layouts.head")
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
                            <img src="{{asset('front/image/step4.png')}}">
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
                <div class="resetPasswordForm">
                    <p class="msg_error"></p>
                    <form class="ui form" id="resetPasswordOne">
                        {{csrf_field()}}
                        <div class="field">
                            @if(Auth::guard('front')->check())
                            <input type="text" @if(request()->user('front')->phone) disabled @endif name="phone" value="{{request()->user('front')->phone}}" id="resetPwPhone" placeholder="请输入您的手机号">
                            @else
                                <input type="text" name="phone" id="resetPwPhone" placeholder="请输入您的手机号">
                            @endif
                        </div>
                        <div class="field">
                            <div class="fields">
                                <div class="eleven wide field">
                                    <input type="text" name="captcha" id="resetPwCaptcha" placeholder="请输入图文验证码">
                                </div>
                                <div class="five wide field">
                                    <img src="{{captcha_src()}}" id="captcha_img" onclick="changeCaptcha()">
                                </div>
                            </div>
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
    function changeCaptcha() {
        $.get('captcha', function (res) {
            $('#captcha_img').attr('src', res.data+'?v='+Math.random());
        });
    }
    $(function(){
        $('.next').on('click', function(){
            var form = $('form').serialize();
            if($('#resetPwPhone').attr('disabled')) {
                form += '&phone=' + $('#resetPwPhone').val();
            }
            $.post('oneForget', form, function(res){
                if(res.code != 200) {
                    if(res.data == '请输入正确的图文验证码') {
                        changeCaptcha();
                    }
                    msg_error(res.data);
                } else {
                    location.href = '/getTwoForget';
                }
            });
        })
    })
</script>
