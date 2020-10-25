@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/base.css?v=1">
    <link rel="stylesheet" href="/tender/css/user.css">
</head>
<body >
<div class="wrap">
    <div class="main">
        <div class="header_nav">
            <img class="txlogo" src="{{$wx['headimg']}}" alt="">
            <div class="user_info">
                <p class="user_name">{{$wx['nick']}}</p>
                <p class="user_price">{{$member->tender_asset ? $member->tender_asset->amount : 0}}</p>
                <p class="user_phone">{{$member->phone}}</p>
            </div>
        </div>
        <div class="transaction">
            <a class="buy_yyblogo" href="/tender/recharge"><img src="/tender/images/flower_logo.png" alt="">购买小红花</a>
            <a class="exchange_logo" href="/tender/withdraw"><img src="/tender/images/exchange_logo.png" alt="">兑换现金</a>
        </div>
        <div class="section">
            <a class="my_watch" href="/tender/mymsgs">
                <img src="/tender/images/my_watch.png" alt="">
                我的消息 <span class="unread-msg-count" style="position:inherit;"></span>
            </a>
            <a class="my_auction" href="/tender/myauction"><img src="/tender/images/my_auction.png" alt="">我的竞拍</a>
            <a class="my_competition" href="/tender/myguess"><img src="/tender/images/my_competition.png" alt="">我的估价</a>
            <a class="my_bill bornone" href="/tender/mybill"><img src="/tender/images/my_bill.png" alt="">我的流水</a>
            <a class="my_watch" href="/tender/margin"><img src="/tender/images/margin.png" alt="">保证金管理</a>
            <a class="my_watch" href="/tender/logout"><img src="/tender/images/logout.png" alt="">更换账户</a>
        </div>
    </div>
    <!-- 底部 -->
    @include('front.tender.layouts.myfoot')
</div>
<script src="/tender/js/jquery.min.js"></script>
<script src="/tender/js/jquery-weui.min.js"></script>
@include('front.tender.foot')
