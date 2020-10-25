@section('title', '重置交易密码 - 用户管理中心')
@include("front.layouts.head")
<script type="text/javascript" src="../js/jsencrypt.min.js"></script>
<div class="userContainer pusher">
  <div class="ui container">
    <div class="pusher">
        <div class="userChange">
            <div class="ui stackable inverted equal height stackable grid">
                @include('front.layouts.leftTree')
                <div class="thirteen wide column">
                  <div class="articleInfo">
                    <h4 class="ui header">重置交易密码</h4>
                    <div class="resetTradePwForm">
                        <p class="msg_error"></p>
                      <form class="ui form" id="resetTradePwForm">
                      {{csrf_field()}}
                          <textarea id="key" style="display: none">{!! $key !!}</textarea>
                        <div class="field">
                          <input type="text" name="resetTradePwPhone" disabled value="{{request()->user('front')->phone}}" id="resetTradePwPhone" placeholder="请输入您的手机号">
                        </div>
                        <div class="field">
                          <div class="fields">
                            <div class="nine wide field">
                              <input type="text" name="resetTradePwCode" id="resetTradePwCode" placeholder="短信验证码">
                            </div>
                            <div class="seven wide field">
                              <input type="button" id="getVerificationCode" class="ui button primary" value="获取验证码">
                            </div>
                          </div>
                          <p class="verificationCodeReminder">您好，验证码已经发送至您的手机，如没有收到，请在倒计时结束后<a>重新发送</a></p>
                        </div>
                        <div class="field">
                          <input type="password" name="resetTradePwNewPassword" id="resetTradePwNewPassword" placeholder="请设置新的交易密码">
                        </div>
                        <div class="field">
                          <input type="password" name="resetTradePwNewPassword_confirmation" id="resetTradePwNewPassword_confirmation" placeholder="请确认交易密码">
                        </div>
                        <button class="ui button formBtn finish" type="button">完成</button>
                      </form>
                    </div>
                  </div>
                </div>
            </div>
      </div>
    </div>
  </div>
</div>
@include("front.layouts.foot")
<script type="text/javascript">
    $(function(){
        $('#getVerificationCode').on('click', function () {
            var mobile = $('#resetTradePwPhone').val();
            var obj = $(this);
            if(!mobile){
                msg_error('请输入正确的手机号');
                return false;
            }
            $.get('/sendSmsNoAuth?phone='+mobile, function (res) {
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
        $('.finish').on('click', function () {
            var mobile = $('#resetTradePwPhone').val();
            if(!mobile){
                msg_error('请输入正确的手机号', 'resetTradePwPhone');
                return false;
            }
            if(!$('#getVerificationCode').val()) {
                msg_error('请输入短信验证码', 'getVerificationCode');
                return false;
            }
            if(!$('#resetTradePwNewPassword').val()) {
                msg_error('请输入密码', 'resetTradePwNewPassword');
                return false;
            }

            if($('#resetTradePwNewPassword').val().length < 6) {
                msg_error('密码不得少于6位', 'resetTradePwNewPassword');
                return false;
            }

            if(!$('#resetTradePwNewPassword_confirmation').val()) {
                msg_error('请输入重复密码', 'resetTradePwNewPassword_confirmation');
                return false;
            }
            if($('#resetTradePwNewPassword').val() != $('#resetTradePwNewPassword_confirmation').val()) {
                msg_error('密码不一致', 'resetTradePwNewPassword_confirmation');
                return false;
            }
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#resetTradePwNewPassword').val();
            var agin = $('#resetTradePwNewPassword_confirmation').val();
            var code = $('#resetTradePwCode').val();
            var enc = crypt.encrypt(old);
            var ag = crypt.encrypt(agin);
            var c = crypt.encrypt(code);
            $('#resetTradePwNewPassword').val(enc);
            $('#resetTradePwNewPassword_confirmation').val(ag);
            $('#resetTradePwCode').val(c);

            var form = $('form').serialize();
            form += '&resetTradePwPhone=' + $('#resetTradePwPhone').val();
            $('#resetTradePwNewPassword').val(old);
            $('#resetTradePwNewPassword_confirmation').val(agin);
            $('#resetTradePwCode').val(code);
            $.ajax({
                type : "post",
                url : "/member/editResetTradePassword",
                data : form,
                async : false,
                success : function(res){
                    if(res.code != 200) {
                        msg_error(res.data);
                        return false;
                    } else {
                        @if(request()->get('type'))
                            location.href = '/member/resetTradePasswordSuccess?type='+"{{request()->get('type')}}";
                        @else
                            location.href = '/member/resetTradePasswordSuccess';
                        @endif
                    }
                }
            });
            return false;
        })
    })
</script>