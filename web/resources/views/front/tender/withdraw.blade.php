@include('front.tender.head')
    <link rel="stylesheet" href="css/cash-exchange.css">
</head>
<body >
<div class="wrap" style="overflow: auto !important;">
    <div class="main" style="height: auto !important;">
        <div class="header_nav">
            <img class="txlogo" src="images/tx.png" alt="">
            <div class="user_info">
                <p class="user_name">微信昵称</p>
                <p class="user_price">{{$member->tender_asset ? $member->tender_asset->amount : 0}}</p>
                <p class="user_phone">{{$member->phone}}</p>
            </div>
        </div>
        <div class="transaction">
            <span class="buy_yyblogo">
                <h2>已提现金额</h2>
                <h3>￥{{$withrawed}}</h3>
            </span>
            <span class="exchange_logo">
                <h2>可提现金</h2>
                <h3>￥{{$canWithdraw}}</h3>
            </span>
        </div>
        <div class="sr withdraw-sr">
            <input class="card" id="money" type="number" placeholder="请输入提现金额">
            <input class="card" id="card" type="number" placeholder="请输入收款银行账号">
            <input class="card" id="name" type="text" placeholder="请输入收款人姓名">
            <input class="card" id="bank" type="text" placeholder="请输入收款账户开户支行">
            <button id="submit-btn" class="ljcz">
                <span>立即</span>
                <span class="bo">提现</span>
            </button>
            <p class="text1 tx">1.小红花兑换现金申请提交后，平台将在两个工作日内进行审核</p>
            <p class="text1 tx">2.一小红花=一qcash人民币</p>
            <p class="text1">3.手续费按单笔金额收取，T+1到账，每笔收取0.1%，最低1qcash，最高25qcash。</p>
        </div>
    </div>

    <!-- 底部 -->
    @include('front.tender.layouts.myfoot')
</div>
<script src="js/jquery.min.js"></script>
<script src="js/jquery-weui.min.js"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : "{{ csrf_token() }}"
            }
        });
        $('#submit-btn').on('click', function () {
            var money = $('#money').val()
            var card = $('#card').val()
            var name = $('#name').val()
            var bank = $('#bank').val()
            if (money == '' || card == '' || name == '' || bank == '') {
                $.alert('请填完整信息')
                return false;
            }
            $.showLoading()
            $.ajax('/tender/withdraw',{
                method: 'POST',
                dataType: 'json',
                data: {
                    amount: money,
                    card: card,
                    name: name,
                    bank: bank
                },
                success: function (res) {
                    $.hideLoading()
                    $.alert(res.data, function () {
                        if (res.code == 200) {
                            location.href = '/tender/my'
                        }
                    })
                },error: function (err) {
                    $.hideLoading()
                    $.alert(err, function () {
                        location.href = '/tender/my'
                    })
                }
            })

        })

    })

</script>
@include('front.tender.foot')
