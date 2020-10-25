@include('front.tender.head')
    <link rel="stylesheet" href="css/cautionmoney.css">
</head>
<body >
<div class="wrap">
    <div class="main">
        <div class="nav_logo">
            <img src="images/buy_yyblogo.png" alt="">
            <span>{{$margined ? "退还保证金" : "缴纳保证金"}}</span>
        </div>
        <div class="recharge_btn">
            @if($margined)
                <input class="money" id="money" type="number" disabled value="5000">
                <button class="ljcz" id="margin-btn" data-cmd="back">
                    <span>立即</span>
                    <span class="bo">退还</span>
                </button>
            @else
                <input class="money" id="money" type="number" disabled value="5000">
                <button class="ljcz" id="margin-btn" data-cmd="pay">
                    <span>立即</span>
                    <span class="bo">缴纳</span>
                </button>
            @endif
            <p class="rule rules">1.所有用户保证为5000朵小红花。只有缴纳保证金的用户可以参与竞拍！</p>
            <p class="rule">2.只有所参拍拍品全部流程结束，才可申请退还保证金！</p>
        </div>
    </div>
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
        $('#margin-btn').on('click', function () {
            var cmd = $(this).data('cmd')
            $.showLoading()
            $.ajax('/tender/margin',{
                method: 'POST',
                dataType: 'json',
                data: {
                    cmd: cmd
                },
                success: function (res) {
                    $.hideLoading()
                    if(res.code == 209) {
                        $.alert(res.data, function () {
                            location.href = '/tender/recharge'
                        })
                    }else{
                        $.alert(res.data, function () {
                            history.go(-1)
//                            location.href = '/tender/my'
                        })
                    }
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
