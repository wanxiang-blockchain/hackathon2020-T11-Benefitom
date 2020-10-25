@section('title', '艺融宝')
@include("front.rong.head")
<script src="/front/js/jquery-getui.js"></script>
<style>
    .slider {
        position: relative;
        width: 100%;
        z-index: 1;
        height: 420px;
    }
    .slider-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .slider ol, .slider ul,  .slider li {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .slider-item {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 2;
    }
    .slider-item.active {
        z-index: 5;
    }
    .a-img-bg {
        display: block;
        width: 100%;
        -moz-background-size: auto 100%;
        -o-background-size: auto 100%;
        -webkit-background-size: auto 100%;
        background-size: auto 100%;
    }
    .slider-indicator {
        position: absolute;
        left: 50%;
        -webkit-transform: translate(-50%, 0);
        -moz-transform: translate(-50%, 0);
        -ms-transform: translate(-50%, 0);
        -o-transform: translate(-50%, 0);
        transform: translate(-50%, 0);
        bottom: 10px;
        z-index: 20;
        /*width: 1000px;*/
        /*margin-left: -500px;*/
        text-align: center;
    }

    .slider-indicator .active {
        background: #ea564c;
    }
    .slider-indicator li {
        display: inline-block;
        width: 48px;
        height: 8px;
        margin: 0 10px;
        background: #fff;
        cursor: pointer;
        -webkit-transition: all .5s;
        -moz-transition: all .5s;
        -ms-transition: all .5s;
        -o-transition: all .5s;
        transition: all .5s;
    }
    .slider-left-control {
        left: 0;
    }
    .slider-right-control {
        right: 0;
    }
    .slider-left-control, .slider-right-control {
        position: absolute;
        height: 100%;
        width: 15%;
        z-index: 100;
        background-repeat: repeat-x;
        opacity: 0.6;
    }
    .slider-left-control i {
        top: 50%;
        left: 12px;
    }
    .slider-right-control i {
        top: 50%;
    }
    .slider-left-control i, .slider-right-control i {
        display: block;
        position: relative;
        width: 100%;
        height: 100%;
        font-size: 30px;
        color: #fff;
    }
    @media screen and (max-width: 500px) {
        .slider-indicator li {
            width: 8px;
            border-radius: 4px;
        }
        .slider {
            height: 200px;
        }
    }
</style>

<div class="rong-banner">
    <div class="slider">
    <ul class="slider-content">
            <li class="slider-item active" >
                <a class="a-img-bg" href="http://www.yi-yuantuan.com/" target="_blank"
                   style="background-image: url('/rong/image/banner_yuan.jpg?v=2'); background-repeat:  no-repeat;background-position: center;
                           /*filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../image/banner.jpg', sizingMethod='scale')\9;*/
                           height: 100%"></a>
            </li>
            <li class="slider-item active" >
                <a class="a-img-bg" href="/rong/detail/1" target="_blank"
                   style="background-image: url('/rong/image/banner_bi.jpg'); background-repeat:  no-repeat;background-position: center;
                               /*filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../image/banner.jpg', sizingMethod='scale')\9;*/
                               height: 100%"></a>
            </li>
        </ul>
        <ol class="slider-indicator">
            <li></li>
            <li></li>
        </ol>
        <a class="slider-left-control" href="javascript:void(0)"><i class="icon chevron left"></i></a>
        <a class="slider-right-control" href="javascript:void(0)"><i class="icon chevron right"></i></a>
    </div>
</div>
<div class="productList rong">
    <div  class="ui breadcrumb title"> <img class="left-title" src="/rong/image/products_mobile.jpg"> </div>
    {{--<div class="ui two stackable grid">--}}
        {{--<div class="six wide column">--}}
            {{--<a href="http://www.yi-yuantuan.com/"><img src="/rong/image/friend_link.jpg?v=2" /></a>--}}
        {{--</div>--}}
        {{--<div class="ten wide column go-buy">--}}
            {{--特别说明：--}}
            {{--艺援团众筹产品受手机银行限额影响，请在电脑端买入理财产品--}}
            {{--<div class="flex">--}}
                {{--<a href="http://www.yi-yuantuan.com/"> 理财入口：http://www.yi-yuantuan.com/ </a>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    @foreach($models as $value)
    <div class="ui two stackable grid">
        <div class="six wide column">
            <a href="{{'/rong/detail/' . $value['id'] }}"><img src="{{asset('storage/'.$value['picture'])}}" /></a>
        </div>
        <div class="ten wide column go-buy" data-product="1">
            <div  onclick="goBuy({{$value['id']}})" class="title">
                {{$value['name']}}
                <p>
                    <img class="go-buy-img" src="/rong/image/period.jpg"> <img class="go-buy-img" src="/rong/image/go_buy.jpg"></div>
                </p>
            <div class="flex">
                <span>价格{{$value['price']}}</span> | <span>预计年化收益率 {{$value['rate'] * 100}}% </span> | <span> 期限 {{$value['duration']}}个月 </span>
            </div>
            <div class="flex">
                <a target="_blank" href="/rong/image/buy_back_protocol.jpg">《“{{$value['name']}}”艺术品协议收购书》</a>
            </div>
            {{--<div class="ui progress success">--}}
                {{--<div class="bar" style="transition-duration:300ms; width:12%;">--}}
                    {{--<div class="progress"></div>--}}
                {{--</div >--}}
                {{--<div class="label" style="margin-top: 10px;">已售{{$value['sold_amount'] . '/' . $value['amount']}}</div>--}}
            {{--</div>--}}
        </div>
    </div>
    @endforeach
    {{--<div class="ui stackable grid">--}}
        {{--<div class="wide column">--}}
            {{--<a href="https://www.tangcredit.com/"><img src="https://o6ss77ovh.qnssl.com/ACGNOCPG176002866341505986150654_REAR/pc-%E7%AB%AFbanner.jpg" /></a>--}}
        {{--</div>--}}
    {{--</div>--}}
</div>
<div id="my-pro" class="productList rong">
    <div  class="ui breadcrumb title"> <img class="left-title" src="/rong/image/my_product_mobile.jpg"> </div>
    <div class="ui trade_table">
        <div id="app-trade-table" class="flex_table">
            <table class="ui very basic celled ">

                <tbody>
                <tr class="ttitle">
                <td >名称</td>
                <td >年化收益</td>
                <td >期限</td>
                <td >购买时间</td>
                <td >结束时间</td>
                <td >购买价格</td>
                <td >购买数量</td>
                <td >收益</td>
                <td >状态</td>

                </tr>
                @foreach($member->userProducts as $i => $v)
                    <tr>
                        <td class="{{($i%2 == 0) ? 'odd' : ''}}">{{$v->product->name}}</td>
                        <td>{{$v->product->rate * 100}}%</td>
                        <td>{{$v->product->duration}}个月</td>
                        <td>{{$v->created_at}}</td>
                        <td>{{$v->end_at}}</td>
                        <td>{{$v->product->price}}</td>
                        <td>{{$v->amount}}</td>
                        <td>{{$v->earnings()}}</td>
                        <td>{{$v->stat == \App\Model\Product::STAT_HOLD ? "持有" : "结束"}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div style="width: 100%; height: 35px;">

</div>
<script>
    function goBuy(id) {
        location.href = '/rong/buy/' + id
    }
</script>
@include("front.rong.foot")
