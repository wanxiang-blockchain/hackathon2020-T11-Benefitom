@section('title', '找回密码第三步 - 用户管理中心')
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
                    <div class="active step">
                        <h5 class="ui icon header">
                            <img src="{{asset('front/image/step_active2.png')}}">
                            <div class="content">重置密码</div>
                        </h5>
                    </div>
                    <div class="active step">
                        <h5 class="ui icon header">
                            <img src="{{asset('front/image/step_active3.png')}}">
                            <div class="content">完成</div>
                        </h5>
                    </div>
                </div>
                <div class="resetPasswordFinish">
                    <img src="{{asset('front/image/huge_success.png')}}">
                    <p>恭喜您，密码找回成功！<br/><span class="times"> 5 </span>秒钟后，自动跳转到登录页，或<a href="{{route('login')}}">直接跳转</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
<script>
    var time=setInterval (showTime, 1000);
    var second=5;
    function showTime()
    {
        if(second==0)
        {
            window.location="/login";
            clearInterval(time);
        }
        $(".times").html(' '+second+' ');
        second--;
    }
</script>
