<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>艺行派服务协议</title>
    <meta name='viewport', content='width=device-width, initial-scale=1.0' />
    <meta http-equiv='X-UA-Compatible', content='ie=edge' />
    <link rel="shortcut icon" href="{{'/front/favicon.ico'}}">
    <style>
        h2{text-align: center;}
        .xieyi{padding:0 50px;}
        strong{padding-right: 10px;}
    </style>
</head>
<body>
<div class="xieyi">
    <h2>艺行派数据统计</h2>
    <p>
        ARTTBC 购买积分(今日/累计)</p>
    <p>{{$today_bt_amount}}/{{$total_sum_bt + $today_bt_amount}}</p>
    <p>ArTBC + RMB 购买积分(今日/累计)</p>
    <p>{{$today_artbc_amount}}/{{$total_sum_artbc + $today_artbc_amount}}</p>
    <p>RMB 购买积分(今日/累计)</p>
    <p>{{$today_rmb_amount}}/{{$total_sum_rmb + $today_rmb_amount}}</p>
    <p>ARTBCS + RMB 购买积分(今日/累计)</p>
    <p>{{$today_artbcs_amount}}/{{$total_sum_artbcs + $today_artbcs_amount}}</p>
    <p>注册量(今日/累计)</p>
    <p>{{$today_member_sum}}/{{$total_member_sum + $today_member_sum}}</p>
    <p>支付宝提现(今日/累计)</p>
    <p>{{$today_alipay_draw_sum}}/{{$total_alipay_draw_sum + $today_alipay_draw_sum}}</p>
    <p>支付宝充值(今日/累计)</p>
    <p>{{$today_ali_recharge_sum}}/{{$total_ali_recharge_sum + $today_ali_recharge_sum}}</p>
</div>

</body>
</html>
