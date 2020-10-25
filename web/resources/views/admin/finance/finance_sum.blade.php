
@extends('layouts.admin')

@section('title', '财务统计')
    <style>
        .datas{
            width: 100%;
            display: flex;
            flex-direction: row;
            text-align: center;
            padding: 25px;
            font-size: 2rem;
            justify-content: space-around;
        }
        @media screen and (max-width: 500px){
            .datas{
                font-size: 1rem;
            }
        }
        .datas > .column{
            width: 28%;
            border: 1px solid #e5e3e4;
            background-color: #d0e9c6;
            padding: 25px;
        }
        .datas > .column > p:last-child{
            color: red;
        }
    </style>

@section('content')
    <div class="page-title">
        <h2>财务统计</h2>
    </div>
    <div class="datas">
        <div class="column">
            <p>支付宝充值总额</p>
            <p>{{$ali_recharge}}</p>
        </div>
        <div class="column">
            <p>后台大额充值总额</p>
            <p>{{$admin_recharge}}</p>
        </div>
        <div class="column">
            <p>交易手续费总额</p>
            <p>{{$trade_fee}}</p>

        </div>

    </div>
    <div class="datas">
        <div class="column">
            <p>用户持有现金数量</p>
            <p>{{$member_balance}}</p>
        </div>
        <div class="column">
            <p>用户持有楼盘数量</p>
            <p>{{$member_shares}}</p>
        </div>
        <div class="column">
            <p>支付宝提现总额</p>
            <p>{{$withdraw_amount}}</p>
        </div>
    </div>

    <div class="datas">
        <div class="column">
            <p>平台交易总额</p>
            <p>{{$trade_total}}</p>
        </div>
        <div class="column">
            <p>已提货楼盘数</p>
            <p>{{$delivery_amount}}</p>
        </div>
        <div class="column">
            <p>交易产生积分总额</p>
            <p>{{$score_amount}}</p>
        </div>
    </div>



@endsection
<script>
    function href(obj){
        var  url = $(obj).data('url');
        location.href=url;
    }
</script>