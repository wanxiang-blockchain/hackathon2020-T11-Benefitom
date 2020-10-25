@section('title', $message)
@include("front.layouts.head")
<div class="pusher login">
    <div class="ui container certification_common">
        <div class="ts_certivication">
            <div class="loginSuccess">
                <h1 class="ui header">

                    <div class="content" style="font-size: 22px;">
                        {{$message}}
                        {{-- <div class="sub header">3秒后进入首页</div>--}}
                    </div>
                </h1>
                <div style="width: 100%;text-align: center;margin-top: 15px;">
                    <img src="{{asset('front/image/reg_success.png')}}">
                </div>
                <div style="width: 100%;text-align: center;margin-top: 15px;">
                    <a href="/subscription" style="background: #ea564c;color:#fff" class="ui button formBtn button" type="submit">查看认购项目</a>
                </div>
                <div style="width: 100%;text-align: center;margin-top: 15px;">
                    <a href="/member" style="background: #edded0" class="ui button formBtn button" type="submit">去往用户管理中心</a>
                </div>
            </div>

        </div>
    </div>
</div>
@include("front.layouts.foot")
{{-- <script>
    var time=setInterval (showTime, 1000);
    var second=3;
    function showTime()
    {
        if(second==0)
        {
            window.location="/";
            clearInterval(time);
        }
        $(".sub.header").html(second+'秒后返回');
        second--;
    }
</script>--}}
