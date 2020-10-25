<?php
/**
 * 1、楼盘代码
2、楼盘名称
3、现价：最新成交价格
4、涨幅%：（现价-昨日收盘价）/昨日收盘价
5、涨跌：（现价-昨日收盘价）
6、成交量：当天实时的成交量总和（单向计算）
7、成交额：当天实时的成交金额总和（单向计算）
8、换手%：成交量/总发行量*100%
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/17
 * Time: 下午2:47
 */
?>
<div class="ui container trade_table">
    <p style="color: #cd9f7d; font-size: 18px;">
        交易品种
    </p>
    <div id="app-trade-table" class="flex_table">
        <table class="ui very basic celled ">
            <tr class="ttitle">
                <td>楼盘代码</td>
                <td>名称</td>
                <td>现价</td>
                <td>涨幅%</td>
                <td>涨跌</td>
                <td>成交量</td>
                <td>成交额</td>
                {{--<td>换手%</td>--}}
            </tr>
            <tr v-for="item in list" :class="item.increase >= 0 ? 'incre' : 'decre'">
                <td>@{{ item.code }}</td>
                <td><a :href="'/trade/detail/' + item.id">@{{ item.name}}</a></td>
                <td>@{{ item.price}}</td>
                <td>@{{ item.increase_amount}}</td>
                <td>@{{ item.increase}}</td>
                <td>@{{ item.total_amount}}</td>
                <td>@{{ item.total_balance}}</td>
                {{--                <td>@{{ item.change}}</td>--}}
            </tr>
        </table>
    </div>

</div>
<style>
    .ttitle{
        background-color: #FF8352;
    }
    .incre {color: #FF472F;}
    .decre {color: #4AC818;}
    .trade_table table{
        background: #ffffff;
    }
    .trade_table table td{
        border: 1px solid #CDCDCD;
        border-color: #CDCDCD;
        text-align: center;
    }
</style>

<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script type="text/javascript" src="/front/js/vue.min.js"></script>
<script>
    var tradeTable = new Vue({
        el: "#app-trade-table",
        data: {
            list: [ ]
        }
    })

    $.get('/chart/tradeTable', {}, function (res) {
        if(res.code == 200) {
            tradeTable.list = res.data
        }
    })
</script>