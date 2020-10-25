@section('title', '交易中心')
@include("front.layouts.mhead")
</head>
<style>
    html, body{
        height: 100%;
        overflow: hidden;
        background-color: #000000;
        color: #ffffff;
    }
    nav{
        height: 0%;
        text-align: center;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }
    #vue-trade-table{
        height: 100%;
    }
    #tradeListBox{
        height: 50%;
        overflow: hidden;
    }
    #tradeListBox > table{
        width:100%;
        border-collapse: collapse;
    }
    #summary{
        height: 50%;
        background-color: #222021;
        margin-top: 0.5rem;
    }
    #tradeListBox > table  td{
        border-bottom: 1px solid #4e4e4e;
    }
    #tradeListBox > table > tbody > tr > td, #tradeListBox > table > thead > tr > th{
        text-align: right;
        padding:0.5rem 5%;
    }
    #tradeListBox > table > tbody > tr > td:first-child, #tradeListBox > table > thead > tr > th:first-child{
        text-align: left;
    }

    #tradeListBox .incre span, #tradeListBox .decre span{
        padding: 0.1rem 0.5rem;
        border-radius: 3px;
        width: 3.5rem;
        display: block;
    }
    #tradeListBox .decre span{
        background-color: #69d05d;
    }
    #tradeListBox .incre span{
        background-color: #f6424e;
    }
    #summary{
        text-align: center;
    }
    #summary > table, #summary > p, #summary > div {
        width: 90%;
        margin: 0 auto;
    }
    #summary > table{
        border-collapse: collapse;
    }
    #summary > p{
        height: 4rem;
        line-height: 4rem;
        border-bottom: 3px solid #4e4e4e;
    }
    #summary > table  td{
        height: 2.5rem;
        line-height: 2.5rem;
        border-bottom: 1px solid #4e4e4e;
    }
    @media screen and (min-height: 600px){
        #summary > table  td{
            height: 3.5rem;
            line-height: 3.5rem;
            border-bottom: 1px solid #4e4e4e;
        }
    }
    #summary > div > a{
        display: inline-block;
        background-color: rgb(231, 231, 233);
        color: rgb(70, 70, 70);
        float: right;
        margin-top: 1.5rem;
        padding: 0.5rem 1rem;
        border-radius: 5px;
    }
    #tradeListBox > table > tbody > tr.active{
        background-color: #313131;
    }
</style>
<body>
{{--<nav>--}}
    {{--益通云楼盘行情中心--}}
{{--</nav>--}}
<div id="vue-trade-table">
    <div id="tradeListBox">
        <table cellspacing="0">
            <tbody>
            <tr v-on:click="quotation(item.id)" v-for="item in list" :class="{incre: item.increase >=0, decre:item.increase<0, active:item.id == currentId}">
                <td>@{{ item.name}}</td>
                <td>@{{ item.price}}</td>
                <td><span>@{{ item.increase >= 0 ? '+' + item.increase_amount : item.increase_amount}}%</span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="summary">
        <p>@{{summary.asset_name}}</p>
        <table>
            <tr>
                <td>开盘价</td>
                <td>@{{ summary.openPrice }}</td>
                <td>昨收价</td>
                <td>@{{ summary.trade_price}}</td>
            </tr>
            <tr>
                <td>最高价</td>
                <td>@{{ summary.maxPrice }}</td>
                <td>最低价</td>
                <td>@{{ summary.minPrice}}</td>
            </tr>
            <tr>
                <td>成交量</td>
                <td>@{{ summary.sumAmount }}</td>
                <td>成交额</td>
                <td>@{{ summary.sumPrice}}</td>
            </tr>
        </table>
        <div>
            <a :href="'/trade/detail/' + currentId">去交易</a>
        </div>
    </div>

</div>
<script type="text/javascript" src="/front/js/vue.min.js"></script>
<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script>
    $(function () {

        var tradeTable = new Vue({
            el: "#vue-trade-table",
            data: {
                lastId: 0,
                list: [ ],
                summary: {},
                currentId: 0
            },
            methods: {
                quotation: function (id) {
                    var _this = this
                    this.currentId = id
                    $.ajax('/trade/ajaxDetail/' + id, {
                        dataType: 'json',
                        method: 'get',
                        success:  function (res) {
                            if (res.code == 200) {
                                _this.summary = res.data.summary
                            }
                        }
                    })
                }
            }
        })

        function fetchList() {
            $.get('/chart/tradeTable', {
                lastId: tradeTable.lastId,
                limit: 5
            }, function (res) {
                if(res.code == 200) {
                    tradeTable.list = res.data
                    tradeTable.lastId = res.lastId
                    if(tradeTable.currentId == 0 && tradeTable.list.length){
                        // init
                        tradeTable.quotation(tradeTable.list[0].id)
                    }
                    var t = setTimeout(fetchList, 5000);
                }
            })
        }
        fetchList()
    })

</script>
@include("front.layouts.mfoot")
