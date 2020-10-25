@section('title', '交易-绍德艺品易货中心')
@include('front.layouts.mhead')
    <style>
        body{
            background-color: #000000;
            height: 100%;
            color: #ffffff;
        }
        nav{
            display: flex;
            justify-content: space-around;
            text-align: center;
            font-size: 1rem !important;
            margin-top: 1.2rem;
        }
        nav > span{
            height: 100%;
        }
        nav > span:first-child{ width: 10%; }
        nav > span:nth-child(2n){ width: 80%; }
        nav > span:last-child{ width: 10%; }
        nav > span > a > img{width: 1.2rem;}
        #price {
            height: 5rem;
            margin-top: 1rem;
            border-top: 1px solid #313131;
        }
        #price > div{
            height: 100%;
            float: left;}
        #price > .price-left{
            width: 50%;
        }
        #price > .price-right{
            width: 45%;
        }
        #price > .price-left > div, #price > .price-right{
            float: left;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
        }
        #price > .price-left > div:first-child{
            font-weight: 400;
            font-size: 2.5rem;
            width: 50%;
            padding-left: 6%;
        }
        #price > .price-left > div:last-child{
            width: 40%;
            text-align: right;
        }
        #price > .price-right > p{
            background-color: #222021;
            padding: 0.2rem 0rem 0.2rem 1rem;
            border: 1px solid #313131;
            font-size: 0.8rem;
        }
        #k-line{
            background-color: #313131;
            /*margin-top: 1rem;*/
            border-top: 1px solid #313131;
        }
        #last-trade{
            margin-top: 1rem;
        }
        #last-trade > table{
            width: 80%;
            margin:0 auto;
            background-color: #222021;
            padding:0.2rem 0.8rem;
        }
        #trade {
            margin-top: 1rem;
        }
        #trade > div{
            float:left;
        }
        #trade button{
            background: #f8f8f8;
            color: #737375;
            padding: 0.4rem 0.6rem;
            font-size: 1rem;
            font-weight: 400;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        #trade > .trade-left{
            width: 60%;
            margin-left: 10%;
        }
        #trade > .trade-right{
            width: 26%;
        }
        #trade .trade-sell{
            visibility: hidden;
        }
        #trade .trade-foot{
            height: 4rem;
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        #trade .trade-foot input{
            background-color: #828081;
            border: 1px solid #9d9d9d;
            height: 1.5rem;
            width: 7rem;
        }
        #trade > .trade-right button{
            width: 90%;
        }
        #submit{
            height: 100%;
            font-size: 1.5rem !important;
        }
        #trade > .trade-left > div > button:last-child{
            margin-left: 1rem;
        }
        .yiclock{
            font-size: 10px;
        }
    </style>
</head>
<body>
<nav>
    <span><a href="{{$prev ? '/trade/detail/' . $prev['id'] : '#'}}"><img src="/front/image/trade/left.png"> </a> </span>
    <span>{{$asset_type->name}}</span>
    <span><a href="{{$next ? '/trade/detail/' . $next['id'] : '#'}}"><img src="/front/image/trade/right.png"> </a></span>
</nav>
<input type="hidden" id="hidden_trade_id" value="{{ $asset_type->id }}" />
<section>
    <input type="hidden" id="fall_limit" value="{{$asset_type->fall_limit}}">
    <input type="hidden" id="rise_limit" value="{{$asset_type->rise_limit}}">
    <div id="price">
        <div class="price-left" :style="priceStyle">
            <div>@{{ con.latestPrice }}</div>
            <div>
                <p>@{{ con.increase >= "0" ? "+" : "" }}@{{ con.increase_amount }}</p>
                <p>@{{ con.increase >= "0" ? "+" : "" }}@{{ con.increase }}</p>
            </div>
        </div>
        <div class="price-right">
            <p>买盘：@{{ con.bestSell }}（@{{ con.buyOrderCount }}）</p>
            <p>卖盘：@{{ con.bestBuy }}（@{{ con.sellOrderCount }}）</p>
        </div>
    </div>
    <div id="last-trade">
        <table>
            <tr v-if="trade_logs.length">
                <td>最新成交</td>
                <td>@{{ trade_logs[0].created_at }}</td>
                <td>@{{ trade_logs[0].price }}</td>
                <td>@{{ trade_logs[0].amount}}</td>
            </tr>
        </table>
    </div>
    <div id="trade">
        <div class="trade-left">
            <div>
                <button value="buy" id="buy" v-on:click="buy" name="买入">买 入</button>
                <button value="sell" id="sell" v-on:click="sell" name="卖出">卖 出</button>
                <span class="yiclock"></span>
            </div>
            <div class="trade-buy trade-foot">
                <p><span>@{{ price_label }}：</span><input id="trade-price" value=""></p>
                <p><span>@{{ amount_label }}：</span><input id="trade-amount" value=""></p>
            </div>
        </div>
        <div class="trade-right">
            <div>
                <button onclick="javascript:location.href='/trade/myentrust/{{$asset_type->code}}'" name="我的委托">我的委托</button>
            </div>
            <div class="trade-foot trade-buy">
                <button id="submit" data-type="1" name="确认">确 认</button>
            </div>
            <input type="hidden" id="key" value="{{$key}}" />
            <input type="hidden" id="asset_type" value="{{$asset_type['code']}}">
        </div>
    </div>
</section>
<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script type="text/javascript" src="/front/js/vue.min.js"></script>
<script type="text/javascript" src="/js/admin/plugins/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript" src="/js/jsencrypt.min.js"></script>
<script>
    $(function () {
//        Vue.config.devtools = true

        $('.yiclock').text((new Date()).toLocaleTimeString())
        setInterval(function () {
            var d = new Date()
            $('.yiclock').text(d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds())
        }, 1000)
        // 行情概述
        var priceApp = new Vue({
            el: '#price',
            data: {
                con: {},
                priceStyle:{
                    color: "red"
                },
                bestBuy: 0,
                bestSell: 0
            },
            watch: {
                con: function (val) {
                    if(val.increase_amount > 0){
                        this.priceStyle.color = 'red'
                    }else if(val.increase_amount == 0){
                        this.priceStyle.color = 'white'
                    }else{
                        this.priceStyle.color = 'green'
                    }
                }
            }
        })

        var lastTradeApp = new Vue({
            el: '#last-trade',
            data: {
                trade_logs: []
            }
        })

        var id = $('#hidden_trade_id').val()

        var appedTradeAppData = function () {
            $.ajax('/trade/ajaxDetail/' + id, {
                dataType: 'json',
                method: 'get',
                success:  function (res) {
                    if (res.code == 200) {
                        priceApp.con = res.data.summary
                        lastTradeApp.trade_logs = res.data.trade_logs
                    }
                    if(window.yidebug!=true){
                        var t = setTimeout(appedTradeAppData, 1000);
                    }
                }
            })
        }

        appedTradeAppData()

        var tradeApp = new Vue({
            el: "#trade",
            data: {
                price_label: '买入价格',
                amount_label: '买入数量',
                type: 1
            },
            methods: {
                sell: function (e) {
                    console.log(e);
                    e.preventDefault();
                    this.price_label = '卖出价格';
                    this.amount_label = '卖出数量'
                    this.type = 2
                },
                buy: function (e) {
                    e.preventDefault();
                    this.price_label = '买入价格';
                    this.amount_label = '买入数量'
                    this.type = 1
                }
            }
        })

        var makeOrder = function(form){

            $.post('/trade/tradeOrder', form, function (res) {
                $('#submit').prop('disabled', '')
                if(res.code == 204) {
                    swal({
                        title: "余额不足!",
                        text: res.data,
                        html: true
                    }, function () {
                        location.href = '/member/recharge'
                    })
                } else if(res.code == 200) {
                    swal(res.data);
                    // 存储trade_remerber_token
                    sessionStorage.setItem('trade_remember_token', res.remember_token)
                } else if(res.code == 202) {
                    // 需要输入交易密码
                    sessionStorage.removeItem('trade_remember_token')
                    swal(res.data);
                } else {
                    swal({
                        title: "HTML <small>提示</small>!",
                        text: res.data,
                        html: true
                });
                }
            },'json');
        }

        $('#submit').on('click', function () {
            //确认操作
            $(this).prop('disabled', 'disabled')

            var data = {
                type: tradeApp.type,
                asset_type: $('#asset_type').val()
            }

            var price = $('#trade-price').val();
            var amount = $('#trade-amount').val();
            if(price == ''){
                $('#submit').prop('disabled', '')
                return swal('请输入' + tradeApp.price_label);
            }
            if(amount == ''){
                $('#submit').prop('disabled', '')
                return swal('请输入' + tradeApp.amount_label);
            }
            if (tradeApp.type == 1){
                data.buyPrice = price
                data.buyAmount = amount
            }else{
                data.sellPrice = price
                data.sellAmount = amount
            }

            data.remember_token = sessionStorage.getItem('trade_remember_token')
            data.tradePassword = ''

            if(!data.remember_token){
                // 如果没有remember_token
                swal({
                        title: "请输入您的交易密码！",
                        text: "请输入您的交易密码：",
                        type: "input",
                        inputType: "password",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        animation: "slide-from-top",
                        inputPlaceholder: "请输入您的交易密码"
                    },
                    function(inputValue){
                        var crypt = new JSEncrypt();
                        var key   = $('#key').val();
                        crypt.setKey(key);
                        var enc = crypt.encrypt(inputValue);
                        data.tradePassword = enc
                        makeOrder(data)
                });
            }else{
                makeOrder(data)
            }
        })
    })
</script>
@include('front.layouts.mfoot')