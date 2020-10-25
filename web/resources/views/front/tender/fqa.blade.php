@include('front.tender.head')
    <link rel="stylesheet" href="css/auction-contract.css">
    <style>
        .wrap, .main{
            overflow: scroll;
        }
        .main {
            line-height: 26px;
            left: 0;
            top: 0;
            box-sizing: unset;
            padding-top: 25px;
        }
        .main h3{
            font-weight: bolder;
        }
        .main p{
            text-indent: 32px;
        }
        .section{
            margin-top: 13px;
            padding-bottom: 13px;
        }
    </style>
</head>
<body >
<div class="wrap">
    <div class="main">
        <p class="nav">艺奖堂常见问题</p>
        <div class="section">
            <h3>FAQ1: 为什么我无法参与竞拍出价？</h3>
            <p>答：参与暗标拍品和竞拍拍品的出价需要缴纳5000朵小红花作为保证金。其中，参与多个拍品只需缴纳一次保证鑫。当所有参拍拍品均拍卖结束后，可退还保证金。</p>
        </div>
        <hr/>
        <div class="section">
            <h3>FAQ2: 为什么我的小红花持有数目与可提现金额不等？</h3>
            <p>答：艺奖堂用户首次登录平台赠送10朵小红花，连续签到三天每天赠送10朵小红花，每七天一个循环。其中平台所赠送小红花只可用于拍品估价，不可提现。</p>
        </div>
    </div>
</div>
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

