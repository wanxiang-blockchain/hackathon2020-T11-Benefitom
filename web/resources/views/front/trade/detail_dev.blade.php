@section('title', '交易中心')
@include("front.layouts.head")
<script type="text/javascript" src="/js/echarts.min.js"></script>
<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script type="text/javascript" src="/front/js/vue.min.js"></script>
<script type="text/javascript" src="/front/js/yi.util.js"></script>
@if ($isInTradeTime)
    <script>
        window.isInTradeTime = true
    </script>
@else
    <script>
        window.isInTradeTime = false
    </script>
@endif
<div class="pusher tradeDetails">
    <input type="hidden" id="hidden_trade_id" value="{{ $asset_type->id }}" />
    <div class="ui container">
        <div class="ui breadcrumb">
            <a class="section href=#">交易中心</a>
            <i class="right angle icon divider"></i>
            <div class="active section">{{$asset_type->name}}详情</div>
        </div>

        <div class="tradeDetailsProject">
            <div class="ui items">
                <div class="item">
                    <div class="image">
                        <img src="{{asset('storage/'.$project['picture'])}}">

                    </div>
                    <div class="content">
                        <a class="header">{{$asset_type->name}}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="userEntrust">
            <div class="ui middle stackable grid container">
                <div class="row">
                    <div id="app-buy-sell" class="five wide column">
                        <div class="buySell">
                            <div class="ui tabular menu">
                                <div class="item active" id="buyTab" data-tab="tab-buy">买入</div>
                                <div class="item" id="sellTab" data-tab="tab-sell">卖出</div>
                            </div>
                            <div class="ui tab active" data-tab="tab-buy">
                                <div class="buying">
                                    <h3 class="ui header">
                                        买入  <span class="yiclock"></span>
                                        <a href="/trade_xieYi"  class="trade_rule_link" target="_blank">交易规则</a>
                                    </h3>
                                    <form class="ui form"  method="post" id="mairu">
                                        {{csrf_field()}}
                                        <input type="hidden" name="type" value="1">
                                        <input type="hidden" name="asset_type" value="{{$asset_type['code']}}">
                                        <p>可用余额：￥@{{ balance }}</p>
                                        <input type="hidden" id="balance" :value="balance" />
                                        <div class="field">
                                            <label>买入价格：</label>
                                            <input type="number" step="0.1" value="" autocomplete="off" name="buyPrice" class="buyPrice">
                                        </div>
                                        <div class="field">
                                            <label>买入数量：</label>
                                            <input type="number" autocomplete="off"  name="buyAmount"  min="1" step="1" :placeholder="'最大' + maxBuy" class="buyAmount">
                                        </div>
                                        <div class="field tradePasswordDiv">
                                            <label>交易密码：</label>
                                            <input type="password" style="position: absolute; top: -9999px;" name="tradePassword8" class="tradePassword8">
                                            <input type="password" autocomplete="off" name="tradePassword" class="tradePassword">
                                        </div>
                                        <div class="field">
                                            <label>交易金额：</label>
                                            <span><span class="buyTotal">0</span>qcash</span>
                                            <a href="{{route('member/resetTradePassword')}}">忘记密码</a>
                                        </div>
                                        @if($begin == 1)
                                            <button class="ui button" style="background: #999a9c" onclick="show_error(' 请先认购,认购期结束后开启')" type="button">暂未开启</button>
                                        @else
                                            <button class="ui button mairu_button" type="button">{{\App\Service\TradeSetService::isInTradeTime($trade_set) ? '买入' : '不在交易时间'}}</button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                            <div class="ui tab" data-tab="tab-sell">
                                <div class="selling">
                                    <h3 class="ui header">卖出  <span class="yiclock"></span>
                                        <a href="/trade_xieYi" class="trade_rule_link" target="_blank">交易规则</a>
                                    </h3>
                                    <form class="ui form" method="post" id="maichu">
                                        {{csrf_field()}}
                                        <input type="hidden" name="type" value="2">
                                        <input type="hidden" name="asset_type" value="{{$asset_type['code']}}">
                                        <p>持有：@{{ hold_amount }}份</p>
                                        <div class="field">
                                            <label>卖出价格：</label>
                                            <input type="number" step="0.1" value="bestSell" autocomplete="off" name="sellPrice" class="sellPrice">
                                        </div>
                                        <div class="field">
                                            <label>卖出数量：</label>
                                            <input type="number" autocomplete="off" min="1" step="1" :placeholder="'最大' + maxSell" name="sellAmount" class="sellAmount">
                                        </div>
                                        <div class="field tradePasswordDiv">
                                            <label>交易密码：</label>
                                            <input type="password" style="position: absolute; top: -9999px;" name="tradePassword9" class="tradePassword9">
                                            <input type="password" autocomplete="off" name="tradePassword" class="tradePassword1">
                                        </div>
                                        <div class="field">
                                            <label>交易金额：</label>
                                            <span><span class="sellTotal">0</span>qcash</span>
                                            <a href="{{route('member/resetTradePassword')}}">忘记密码</a>
                                        </div>
                                        @if($begin == 1)
                                            <button class="ui button" style="background: #999a9c" onclick="show_error(' 请先认购,认购期结束后开启')" type="button">暂未开启</button>
                                        @else
                                            <button class="ui button maichu_button" type="button">{{\App\Service\TradeSetService::isInTradeTime($trade_set) ? '卖出' : '不在交易时间'}}</button>
                                        @endif
                                    </form>
                                    <textarea id="key" style="display: none">{!! $key !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ui small modal show_error confirmCommon">
    <div class="header" style="color:#333;">
        温馨提示
    </div>
    <div class="content message_content">

    </div>
    {{-- <div class="actions">
        <div class="ui positive right labeled icon button">确认</div>
    </div>--}}
</div>
<!-- 确认买入弹窗 -->
<div class="ui small modal confirmBuy confirmCommon">
    <div class="header">
        确认买入
    </div>
    <div class="content">
        <p>购入楼盘</p>
        <p>需付金额<span>30000qcash</span></p>
    </div>
    <div class="actions">
        <div class="ui negative button">取消</div>
        <button class="ui positive right labeled icon button check_sale">确认</button>
    </div>
</div>
<!-- 确认卖出弹窗 -->
<div class="ui small modal confirmSell confirmCommon">
    <div class="header">
        确认卖出
    </div>
    <div class="content">
        <p>卖出数量：40000份</p>
        <p>卖出价格：<span>30000qcash</span></p>
    </div>
    <div class="actions">
        <div class="ui negative button">取消</div>
        <button class="ui positive right labeled icon button check_sell">确认</button>
    </div>
</div>
<!-- 余额不足弹窗 -->
<div class="ui small modal moneyNotEnough confirmCommon">
    <div class="header">
        余额不足
    </div>
    <div class="content">
        <p>购入楼盘：50000份</p>
        <p>需付金额：60000qcash</p>
        <p>剩余金额：40000qcash</p>
        <p>还差金额：<span>20000qcash</span></p>
    </div>
    <div class="actions">
        <div class="ui negative button">取消</div>
        <a href="/member/recharge" class="ui positive right labeled icon button">立即充值</a>
    </div>
</div>
<!-- 撤单弹窗 -->
<div class="ui small modal inverted confirmCommon">
    <div class="header" style="color: #333">
        主要提示
    </div>
    <div class="content">
        <p>确定撤销此单吗?</p>
    </div>
    <div class="actions">
        <div class="ui negative button">取消</div>
        <a href="javascript:;" class="ui positive right labeled icon button check_inverted" data-id="">确定</a>
    </div>
</div>
{{--<input type="hidden" id="wstoken" value="{{$wstoken}}">--}}

<script type="application/javascript"  src="/front/js/trade_http.js?v=11"></script>
<script>
    $(document).ready(function(){

        sessionStorage.clear()

      $(".buyAmount").val("");
      $(".tradePassword").val("");
      $(".sellAmount").val("");
      $(".tradePassword1").val("");
      var code = '{{$asset_type['code']}}';
    function updataMoney(){
        var buyMoney=$(".buyPrice").val();
        var buyAmount=$(".buyAmount").val();
        var total=(buyMoney*buyAmount).toFixed(2);
        $(".buyTotal").text(total);
    }
    function updataSellMoney(){
        var sellMoney=$(".sellPrice").val();
        var sellAmount=$(".sellAmount").val();
        var total=(sellMoney*sellAmount).toFixed(2);
        $(".sellTotal").text(total);
    }

    function check_remember_token()
    {
        var remember_token = localStorage.getItem('trade_remember_token')
        if (!remember_token) return false;
        // 查看是否过期
        var ts = remember_token.split('|')[0]
        return Date.parse(new Date())/1000 - ts < 1800
    }

    if (check_remember_token()) {
        $('.tradePasswordDiv').hide()
    }


    function check_field(price, amount, password) {
        if(!check_money(price)) {
            show_error('请输入正确的价格');
            return false;
        }
        if(!price || price <=0) {
            show_error('请输入正确的价格');
            return false;
        }
        var val = parseInt(amount);
        var check_amount = Math.floor(val) === val;
        if(!check_amount) {
            show_error('请输入正确的数量');
            return false;
        }
        if(!amount || amount <=0) {
            show_error('请输入正确的数量');
            return false;
        }

        if(!check_remember_token() && !password) {
            show_error('请输入交易密码');
            return false;
        }
        return true;
    }
    $(".buyPrice").change(function(){
        var that=$(this);
        var value = that.val();
        var check_ret = check_money(value);
        if(!check_ret) {
            $(this).val("");
        } else {
            $(this).val(value);
        }
        //  更新maxBuy
        var maxBuy = parseInt($('#balance').val() / value)
        $(".buyAmount").prop('placeholder', '最大' + maxBuy)
        updataMoney();
    })
    $(".buyAmount").change(function(){
        var that=$(this);
        if(that.val().indexOf('.') != -1) {
            var amount=that.val("");
            $(this).val(amount);
        }
        if(/^\\d+$/.test(that.val())){
            var amount=that.val().replace(/^\\d+$/g,'');
            $(this).val(amount);
        }
        updataMoney();
    })
    $(".sellPrice").change(function(){
        var that=$(this);
        var value = that.val();
        var check_ret = check_money(value);
        if(!check_ret) {
            $(this).val("");
        } else {
            $(this).val(value);
        }
        updataSellMoney();
    })
    $(".sellAmount").change(function(){
        var that=$(this);
        if(that.val().indexOf('.') != -1) {
            var amount=that.val("");
            $(this).val(amount);
        }
        if(/^\\d+$/.test(that.val())){
            var amount=that.val().replace(/^\\d+$/g,'');
            $(this).val(amount);
        }
        updataSellMoney();
    });

    //买入准备验证工作
    $('.mairu_button').on('click', function () {
        var buyPrice = $('.buyPrice').val();
        var buyAmount = $('.buyAmount').val();
        var tradePassword = $('.tradePassword').val();
        if(!check_field(buyPrice, buyAmount, tradePassword)) {
            return false;
        };
        var buyTotal = $('.buyTotal').html();
        $('.confirmBuy .content').html('<p>购入楼盘数<span>'+buyAmount+'份</span></p><p>需付金额<span>'+buyTotal+'qcash</span></p>');
        $('.confirmBuy.modal').modal('show');
        return false;
    });
    //确认买入操作
    $('.check_sale').on('click', function () {
        $('.mairu_button').prop('disabled', 'disabled')
        $(this).prop('disabled', 'disabled')
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var old = $('.tradePassword').val();
        var enc = crypt.encrypt(old);
        $('.tradePassword').val(enc);
        var form = $('#mairu').serialize();
        $('.tradePassword').val(old);
        var trade_remember_token = localStorage.getItem('trade_remember_token')
        if(trade_remember_token) form += '&remember_token=' + trade_remember_token
        localStorage.setItem((new Date()).toLocaleString(), 'buy: ' + form)
        $.post('/trade/tradeOrder', form, function (res) {
            if(res.code == 204) {
                $('.moneyNotEnough .content').html(' <p>购入楼盘数：'+$('.buyAmount').val()+'份</p><p>需付金额：'+parseFloat($('.buyTotal').html()).toFixed(2)+'qcash</p> ' +
                    '<p>剩余金额：'+res.data+'qcash</p><p>还差金额：<span>'+(parseFloat($('.buyTotal').html()) - parseFloat(res.data)).toFixed(2)+'qcash</span></p>');
                $('.moneyNotEnough.modal').modal('show');
            } else if(res.code == 200) {
                show_error(res.data);
                // TODO 存储trade_remerber_token
                localStorage.setItem('trade_remember_token', res.remember_token)
                $('.tradePasswordDiv').hide()
            } else if(res.code == 202) {
                // 需要输入交易密码
                $('.tradePasswordDiv').show()
                show_error(res.data);
            } else {
                show_error(res.data);
            }

            setTimeout(function () {
                pageInit()
            }, 2000);
        });
    });

    //卖出准备验证工作
    $('.maichu_button').on('click', function () {
        var sellPrice = $('.sellPrice').val();
        var sellAmount = $('.sellAmount').val();
        var tradePassword = $('.tradePassword1').val();
        if(!check_field(sellPrice, sellAmount, tradePassword)) {
            return false;
        };
        var buyTotal = $('.sellTotal').html();
        $('.confirmSell .content').html('<p>卖出数量：'+sellAmount+'份</p><p>卖出价格：<span>'+buyTotal+'qcash</span></p>');
        $('.confirmSell.modal').modal('show');
        return false;
    });
    //确认卖出操作
    $('.check_sell').on('click', function () {
        $('.maichu_button').prop('disabled', 'disabled')
        $(this).prop('disabled', 'disabled')
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var old = $('.tradePassword1').val();
        var enc = crypt.encrypt(old);
        $('.tradePassword1').val(enc);
        var form = $('#maichu').serialize();
        $('.tradePassword1').val(old);
        var trade_remember_token = localStorage.getItem('trade_remember_token')
        if(trade_remember_token) form += '&remember_token=' + trade_remember_token
        localStorage.setItem((new Date()).toLocaleString(), 'sell: ' + form)
        $.post('/trade/tradeOrder', form, function (res) {
            if(res.code == 200) {
                show_error(res.data);
                localStorage.setItem('trade_remember_token', res.remember_token)
                $('.tradePasswordDiv').hide()
            } else if(res.code == 202) {
                // 需要输入交易密码
                $('.tradePasswordDiv').show()
                show_error(res.data);
            } else {
                show_error(res.data);
            }

            setTimeout(function () {
                pageInit()
            }, 2000);
        });
    });

    $('.check_inverted').on('click', function () {
        var order_id = $(this).attr('id');
        if(!order_id) {
            show_error('参数有误');
            return false;
        }
        $.get('/trade/inverted', {order_id:order_id}, function (res) {
            if(res.code != 200) {
                show_error('失败');
                return false;
            }
            show_error('撤单成功');
            setTimeout(function () {
                pageInit()
            }, 2000);
        });
    });

    function show_error(message) {
        $('.message_content').html(message ? '<p>'+message+'</p>' : '系统错误');
        $('.show_error.modal').modal('show');
    }
        function hide_error() {
            $('.show_error.modal').modal('hide');
        }
        function pageInit() {
            $(".buyAmount").val("");
            $(".tradePassword").val("");
            $(".sellAmount").val("");
            $(".tradePassword1").val("");
            $('.maichu_button').prop('disabled', '')
            $('.mairu_button').prop('disabled', '')
            $('.check_sell').prop('disabled', '')
            $('.check_sale').prop('disabled', '')
            hide_error();
        }
    function check_money(money) {
        var result = true;
        var string_price = money.toString().split(".");
        if(string_price.length > 1) {
            if (string_price.length <= 2) {
                if (string_price[1].length <= 2) {
                    //value.toString();
                } else {
                    result = false;
                }
            } else {
                result = false;
            }
        }
        if(!string_price[0] && !string_price[1]) {
            result = false;
        }
        if(!/(^[1-9]\d*(\.\d{1,2})?$)|(\.)|(^0(\.\d{1,2})?$)/.test(money)){
            result = false;
        }
        return result;
    }


    // 买入卖出记忆功能
    $('.buySell > .tabular > .item').on('click', function () {
        localStorage.setItem('buySellTab', $(this).prop('id'))
    })
        var buySellTabId = localStorage.getItem('buySellTab')
        if(buySellTabId) {
            $('#' + buySellTabId).click()
        }

        $('.yiclock').text((new Date()).toLocaleTimeString())
        setInterval(function () {
            $('.yiclock').text((new Date()).toLocaleTimeString())
        }, 1000)

  })

    // 买一推荐
    $('.buyPrice').on('focus', function () {
        var s = $('#sold1').val()
        if(parseFloat(s) != NaN){
            $(this).val(s);
        }
    })
    // 买一推荐
    $('.sellPrice').on('focus', function () {
        var s = $('#buy1').val()
        if(parseFloat(s) != NaN){
            $(this).val(s);
        }
    })
</script>
@include("front.layouts.foot")
