@section('title', '充值中心 - 用户管理中心')
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name='viewport', content='width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no' />
    <meta http-equiv='X-UA-Compatible', content='ie=edge' />
    <link rel="shortcut icon" href="{{'/front/favicon.ico?v=1'}}">
    <link rel="stylesheet" type="text/css" href="/front/css/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="/front/css/video-js.css">
    <link rel="stylesheet" type="text/css" href="/front/css/common.css?v=13">
    <link rel="stylesheet" type="text/css" href="/front/css/user.css?v=3">
    <link rel="stylesheet" type="text/css" href="/front/css/index.css?v=23">
    <link rel="stylesheet" type='text/css' href="/front/css/newh.css?v=4">
    <link rel="stylesheet" type='text/css' href="/js/admin/plugins/sweetalert/sweetalert.css">
</head>
<body id="tangsheng" class="pushable index">
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
{{--                    @include('front.layouts.leftTree')--}}
                    <div class="thirteen wide column withdraw">
                        {{--<div class="userTop">--}}
                            {{--<div class="ui stackable inverted divided equal height stackable grid">--}}
                                {{--<div style="text-align: center;" class="seven wide column">--}}
                                    {{--<h3>{{number_format($total_amount, 2)}}qcash</h3>--}}
                                    {{--<h4 class="ui header balance">--}}
                                        {{--<img class="ui image" src="{{asset('front/image/money_icon.png')}}">--}}
                                        {{--<div class="content">人民币现金账户管理</div>--}}
                                    {{--</h4>--}}
                                {{--</div>--}}
                                {{--<div class="nine wide column">--}}
                                    {{--<div class="ui inverted divided equal height grid">--}}
                                         {{--<div class="seven wide column">--}}
                                            {{--<a href="{{route('member/recharge')}}" class="rechargeBtn active">现金充值</a>--}}
                                        {{--</div>--}}
                                        {{--<div class="seven wide column">--}}
                                            {{--<a href="{{route('member/withdraw')}}" class="rechargeBtn">现金提现</a>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="userMain">
                            <p>购买ARTTBC，价格：3qcash。</p>
                            <form class="ui form" id="rechangeForm" action="{{route('pay/getRecharge')}}">
                                <div class="field">
                                    <input name="rechangeAmount" id="rechangeAmount" type="number" required min="0.01" step="0.01" placeholder="请输入购买数量">
                                </div>
                                <div class="ui radio " style="margin:20px 0px;">
                                    <input type="radio" name="paytype" value="1" checked="checked">
                                    <label>支付宝</label>
                                    @if(is_weixin())
                                    <input type="radio" name="paytype" value="2" style="margin-left: 15px;">
                                    <label>微信</label>
                                    @endif
                                </div>
                                <input type="submit" id="submit" class="ui orange submit button right formBtn" value='支付'>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div style="width: 100%;height: 4028px;background-color: black;opacity: 0.5;display: none;position: absolute;top: 0;z-index: 999;" id="mask">
&nbsp;
</div>
<div class="ui page dimmer">
    <div class="content">
        <div class="center"></div>
    </div>
</div>
<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script type="text/javascript" src="/front/js/semantic.min.js"></script>
<script type="text/javascript" src="/front/js/WdatePicker.js"></script>
<script type="text/javascript" src="/front/js/jquery.validate.js"></script>
<script type="text/javascript" src="/front/js/videojs-ie8.min.js"></script>
<script type="text/javascript" src="/front/js/messages_cn.js"></script>
<script type="text/javascript" src="/front/js/app.js?v=1"></script>
<script type="text/javascript" src="/front/js/common.js?v=9"></script>
{{-- <script type="text/javascript" src="/front/js/form.js?v=4"></script>--}}
<script type="text/javascript" src="/js/admin/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/front/js/jquery-getui.js"></script>
<script type="text/javascript" src="/js/jsencrypt.min.js"></script>
@stack('endscripts')
<script>
    function isInteger(obj) {
        return Math.floor(obj) === obj
    }
    $(function () {
        $('input[type="number"],input[type="tel"]').on('keyup', function () {
            var val = parseInt($(this).val());
            if(!isInteger(val)) {
                $(this).val("");
            }
        });
    })
</script>
<script>
    $(function () {
        $('.cert').on('click', function () {
            $('.dimmer').dimmer('show');
            $('meta[name=viewport]').prop('content', 'width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=2.0,user-scalable=yes');
            var img = $(this).prop('src')
            $('.dimmer .content .center').html('<img style="width: 100%; max-width: 600px;" src="' + img + '" />')
            $('.dimmer img').on('click', function () {
                $('.dimmer').dimmer('hide');
            })
        })
        $('.dimmer').on('click', function () {
            $('.dimmer').dimmer('hide');
            $('meta[name=viewport]').prop('content', 'width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no');
        })
    })
</script>

<script>
    var id = '';
    $('#rechangeForm').on('submit', function (e) {
        // 如果是支付宝直接提交，如果是微信走ajax
        if($(this).hasClass('disabled')) {
            return false;
        }
        if($('#rechangeAmount').val()) {
            $(this).addClass('disabled');
//            window.setInterval("ajax_check()", 5000);
        }
//        var paytype = $('input[name=paytype]:checked').val();
//        if (paytype == 2){
//            e.preventDefault()
//            if( typeof WeixinJSBridge === 'undefined' ) {
//                swal('提示', '请在微信在打开页面！',"error");
//                return false;
//            }
//            $.post('/wx/recharge', {amount: $('#rechangeAmount').val()}, function (ret) {
//                if (ret.code != 200){
//                    swal('提示', ret.data, 'error')
//                    return false
//                }
//                WeixinJSBridge.invoke('getBrandWCPayRequest', ret.data, function(res) {
//                    switch(res.err_msg) {
//                        case 'get_brand_wcpay_request:cancel':
//                            swal('提示', '用户取消支付！',"error");
//                            break;
//                        case 'get_brand_wcpay_request:fail':
//                            swal('提示', '支付失败！（'+res.err_desc+'）',"error");
//                            break;
//                        case 'get_brand_wcpay_request:ok':
//                            swal('成功', '支付成功！',"success");
//                            break;
//                        default:
//                            swal('提示', '支付失败！',"error");
//                            break;
//                    }
//                });
//            }, 'json');
//
//            return false;
//        }
    })
    function ajax_check() {
        $.get('/member/recharge?op=ajax&log_id='+id, function (res) {
            if(res.code==200) {
                window.location.reload();
            } else {
                id = res.data.id;
            }
        })
    }
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="/js/google-analysis.js"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-46679934-7');
</script>
</body>
</html>
