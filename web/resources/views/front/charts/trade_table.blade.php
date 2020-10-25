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
<style>
    .incre {
        color: red;
    }
    .decre{
        color: green;
    }
    #app-trade-table{
        text-align: left;
    }
</style>
<table id="app-trade-table" class="layui-table">
    <thead>
    <td>楼盘名称</td>
    <td>现价</td>
    <td>涨幅</td>
    </thead>
    <tbody>
    <tr v-for="item in list" :class="item.increase >= 0 ? 'incre' : 'decre'">
        <td><a :href="'/trade/detail/' + item.id">@{{ item.name}}</a></td>
        <td>@{{ item.price}}</td>
        <td>@{{ item.increase_amount}}</td>
        {{--<td>@{{ item.increase}}</td>--}}
        {{--<td>@{{ item.total_amount}}</td>--}}
        {{--<td>@{{ item.total_balance}}</td>--}}
    </tr>
    </tbody>
</table>

<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script type="text/javascript" src="/front/js/vue.min.js"></script>
<script>
    var tradeTable = new Vue({
        el: "#app-trade-table",
        data: {
            lastId: 0,
            list: [ ]

        }
    })


    function getData() {

        $.get('/chart/tradeTable', {lastId: tradeTable.lastId}, function (res) {
            if(res.code == 200) {
                tradeTable.list = res.data
                tradeTable.lastId = res.lastId
            }
        })
    }

    getData();

    var t = setInterval(function(){
        getData()
    }, 5000);

</script>