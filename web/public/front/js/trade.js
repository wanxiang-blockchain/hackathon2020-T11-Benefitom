/**
 * Created by johnShaw on 17/7/13.
 */

// 五档
var tradeApp = new Vue({
    el: '#app-trade',
    data: {
        // 买入委托
        buy_trades: [ ],
        sale_trades: [ ]
    }
})

// 最新成交
var dealApp = new Vue({
    el: '#app-deal',
    data: {
        trade_logs: [ ],
        my_entrust: []
    },
    methods: {
        _inverted: function (id) {
            var order_id = id;
            $('.check_inverted').attr('id', order_id);
            $('.inverted.modal').modal('show');
        }
    }
})

// 买入卖出
var buySellApp = new Vue({
    el: '#app-buy-sell',
    data: {
        balance: 0,
        bestBuy: 1,
        hold_amount: 1,
        bestSell: 1,
        maxBuy: 0,
        maxSell: 0
    }
})

// 行情概述
var summaryApp = new Vue({
    el: '#app-summary',
    data: {
        info: { }
    }
})

var id = $('#hidden_trade_id').val()

var appedTradeAppData = function (id) {
    getWithLock('/trade/ajaxDetail/' + id, {}, function (res) {
        if (res.code == 200) {
            tradeApp.buy_trades = res.data.buy_trades
            tradeApp.sale_trades = res.data.sale_trades
            dealApp.trade_logs = res.data.trade_logs
            dealApp.my_entrust = res.data.my_entrust
            buySellApp.balance = res.data.buySell.balance
            buySellApp.bestBuy = res.data.buySell.bestBuy
            buySellApp.maxBuy = res.data.buySell.maxBuy
            buySellApp.hold_amount = res.data.buySell.hold_amount
            buySellApp.maxSell = res.data.buySell.maxSell
            buySellApp.bestSell = res.data.buySell.bestSell
            summaryApp.info = res.data.summary
        }
    }, 'trade_ajaxDetail_' + id)
}

// appedTradeAppData(id)
// TODO 使用socket重构
$(function () {
    // if(!window.debug) {
    //     setInterval(function () {
    //         appedTradeAppData(id)
    //     }, 1000)
    // }

    window.detailSocket = new WebSocket("wss://" + location.host + ":" + window.ws_port);
    window.wstoken = $('#wstoken').val();


    window.detailSocket.onopen = function (event) {
        window.detailSocket.send(window.wstoken);
        var detailTimer = setInterval(function () {
            if(window.detailSocket) {
                window.detailSocket.send(window.wstoken);
            }
        }, 1000)
    };

    window.detailSocket.onmessage = function (event) {
        var res = JSON.parse(event.data)
        if(res.code == 200) {
            tradeApp.buy_trades = res.data.buy_trades
            tradeApp.sale_trades = res.data.sale_trades
            dealApp.trade_logs = res.data.trade_logs
            dealApp.my_entrust = res.data.my_entrust
            buySellApp.balance = res.data.buySell.balance
            buySellApp.bestBuy = res.data.buySell.bestBuy
            buySellApp.maxBuy = res.data.buySell.maxBuy
            buySellApp.hold_amount = res.data.buySell.hold_amount
            buySellApp.maxSell = res.data.buySell.maxSell
            buySellApp.bestSell = res.data.buySell.bestSell
            summaryApp.info = res.data.summary
        }
    }
})
