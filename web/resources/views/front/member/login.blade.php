@section('title', '登录')
@include("front.layouts.head")
<div class="pusher login">
    <div class="ui middle stackable grid container">
        <div class="ts_login row">
            <div class="ten wide column">
                <img class="ui rounded image" src="{{asset('front/image/logo.png')}}">
            </div>
            <div class="six wide column loginForm">
                <h3>登录</h3>
                <p class="msg_error"><b></b></p>
                <form class="ui form" id="loginForm" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="back_type" value="{{request()->get('back_type', '')}}">
                    <div class="field">
                        <input type="tel" name="phone" id="phone" maxlength="11" placeholder="请输入您的手机号" autocomplete="off">
                    </div>
                    <textarea id="key" style="display: none">{!! $key !!}</textarea>
                    <div class="field">
                        <input type="password" name="password" id="password" placeholder="请输入登录密码" autocomplete="off">
                    </div>
                    <button class="ui button formBtn login_submit button" type="submit">登录</button>
                </form>
                <p class="go_login">还没有账户？<a href="/register">立即注册</a></p>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
<script>
    function isNumber(value) {
        var patrn = /^(-)?\d+(\.\d+)?$/;
        if (patrn.exec(value) == null || value == "") {
            return false
        } else {
            return true
        }
    }

    $(function(){

        var prev_action = "<?=$prev_action?>"

        $('form').on('submit', function(){
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#password').val();
            var enc = crypt.encrypt(old);
            $('#password').val(enc);
            if(!$('#phone').val())
            {
                msg_error('请输入正确的手机号', 'phone');
                $('#password').val(old);
                return false;
            }
            if(!$('#password').val())
            {
                msg_error('请输入密码', 'password');
                $('#password').val(old);
                return false;
            }
            var form = $('form').serialize();
            $('#password').val(old);

            $.post('login', form, function(res){
                if(res.code != 200) {
                    msg_error(res.data);
                } else {
//                    setTimeout(function(){
                        if($('input[name=back_type]').val() != '') {
                            location.href = '/subscription/detail/'+ $('input[name=back_type]').val();
                        } else {
                            location.href = prev_action ? decodeURIComponent(prev_action) : '/logSuccess'
                        }

//                    }, 1000);
                }
            });
            return false;
        })
    })
</script>
