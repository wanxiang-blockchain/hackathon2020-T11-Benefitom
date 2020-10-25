@include('front.tender.head')
    <link rel="stylesheet" href="css/cautionmoney.css">
</head>
<body >
<div class="wrap">
    <div class="main">
        <div class="nav_logo">
            <img src="images/flower_logo.png" alt="">
            <span>购买小红花</span>
        </div>
        <div class="recharge_btn">
            <input class="money" id="money" type="number" placeholder="请输入您要缴纳的金额">
            <button id="pay" class="ljcz">
                <span>立即</span>
                <span class="bo">支付</span>
            </button>
            <p class="rule rules">1.小红花为艺奖堂平台能用虚拟币</p>
            <p class="rule">2.10朵小红花=1qcash人民币</p>
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
        $('#pay').on('click', function () {
            var amount = $('#money').val()
            if (!/^[0-9]+$/.test(amount) || amount < 1) {
                $.toast('购买数量必须大于1个且为整数个')
                return false
            }
            $.showLoading()
            $.ajax({
                type:'post',
                data: {
                    amount: $('#money').val()
                },
                dataType:"json",
                async:false,
                url:'/tender/prepay',
                success:function(res){
                    $.hideLoading()
                    if(res.code != 200){
                        $.toast(res.data, "error");
                        return false;
                    }
                    if( typeof WeixinJSBridge === 'undefined' ) {
                        //alert('请在微信在打开页面！');
                        $.toast('请在微信在打开页面！',"error");
                        return false;
                    }
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest', res.data, function(res) {
                            switch(res.err_msg) {
                                case 'get_brand_wcpay_request:cancel':
                                    $.toast('用户取消支付！',"error");
                                    history.go(-1)
                                    break;
                                case 'get_brand_wcpay_request:fail':
                                    $.toast('支付失败！（'+res.err_desc+'）',"error");
                                    break;
                                case 'get_brand_wcpay_request:ok':
                                    $.toast('支付成功！',"success");
                                    history.go(-1)
                                    break;
                                default:
                                    $.toast('支付失败！',"error");
                                    break;
                            }
                        }
                    );


                },
                error:function(err){
                    //alert(err);
                    $.toast('支付失败！',"error");
                }
            });

        })

    })
</script>
@include('front.tender.foot')
