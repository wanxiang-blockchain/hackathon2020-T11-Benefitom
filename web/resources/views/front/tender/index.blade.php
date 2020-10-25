@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/swiper.min.css">
    <link rel="stylesheet" href="/tender/css/index.css?v=5">

<style>
    .swiper-price{
        height: 25px;
        line-height: 25px;
        overflow: hidden;
    }
</style>
</head>
<body ontouchstart>

<div class="weui-pull-to-refresh__layer">
    <div class='weui-pull-to-refresh__arrow'></div>
    <div class='weui-pull-to-refresh__preloader'></div>
    <div class="down">下拉刷新</div>
    <div class="up">释放刷新</div>
    <div class="refresh">正在刷新</div>
</div>

<div id="float-menu">
    <ul>
        <li onclick="location.href='/tender/course';">
            <img width="25" src="/tender/images/underway.png" alt="">
            <p>课堂</p>
        </li>
    </ul>
</div>
<style>
    #float-menu{
        background: rgba(255, 255, 255, 0.5);
        width: 30px;
        height: 30px;
        position: fixed;
        right: 10px;
        bottom: 80px;
        z-index: 1000;
    }
</style>

<div class="wrap">
    <div class="main">
        <!-- 轮播 -->
        <div style="width:100%;" class="swiper">
            <div class="swiper-container2"  style="position: relative;">
                <div class="swiper-wrapper  smallimg" id="node">
                    @foreach($banners as $banner)
                    <div class='swiper-slide go-detail' data-url="{{$banner->link}}">
                        <img src="{{asset('storage/'.$banner->url)}}" alt="">
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <!-- 拍卖大厅 -->
        <div class="section">
            <span class="auction_hall"><img src="/tender/images/auction_hall.png" alt="">拍卖大厅</span>
            <div class="swiper-price">
                <div class="swiper-wrapper">
                    @foreach($winners as $winner)
                        <div class="swiper-slide">
                            {{--{{$winner->phone()}}估价 <span style="color: indianred">{{$winner->tender->name}}</span> 中 <span  style="color: indianred">{{$winner->bonus}}</span> 个小红花--}}
                            {{$winner->phone()}} 获得 <span  style="color: indianred">{{$winner->bonus}}</span> 朵小红花
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="section_btn">
                <a href="javascript:" class="underway active" name="underway">
                    <i><img src="/tender/images/underway.png" alt=""></i>
                    <span>正在进行</span>
                </a>
                <a href="javascript:" class="wait_start" name="wait_start">
                    <i><img src="/tender/images/wait_starth.png" alt=""></i>
                    <span>马上开始</span>
                </a>
                <a href="javascript:" class="crazy_quiz" name="crazy_quiz">
                    <i><img src="/tender/images/crazy_quizh.png" alt=""></i>
                    <span>已经结束</span>
                </a>
                <a href="javascript:" class="award_ranking" name="award_ranking">
                    <i><img src="/tender/images/award_rankingh.png" alt=""></i>
                    <span>获奖排名</span>
                </a>
            </div>
            <!-- 正在进行 -->
            <div id="underwaybox" class="underwaybox active">
                @foreach($tenderings as $tendering)
                    <a href="/tender/detail/{{$tendering->id}}">
                    @if($tendering->isDark())
                        @include('front.tender.components.dark')
                    @else
                        @include('front.tender.components.auction')
                    @endif
                    </a>
                @endforeach
            </div>
            <!-- 马上开始 -->
            <div id="wait_startbox" class="underwaybox wait_start">
            @foreach($tendertodo as  $todo)
                @if($todo->isDark())
                    @include('front.tender.components.dark_todo')
                @else
                    @include('front.tender.components.auction_todo')
                @endif
            @endforeach
            </div>
            <!-- 已经结束 -->
            <div id="crazy_quizbox" class="underwaybox">
                <!-- 暗标 -->
                <div v-for="model in models" class="dark_mark">
                    <a :href="'/tender/detail/' + model.id">
                    <div class="left">
                        <img :src="model.banner" alt="">
                    </div>
                    <div v-if="model.type == 0" class="right">
                        <p class="prd_name">@{{model.name}}</p>
                        <div class='number'>
                            <span class="auction_num">
                                <h2>@{{ model.dealPrice}}</h2>
                                <h3>成交价</h3>
                            </span>
                            <span class="win_num">
                                  <h2>@{{ model.priceCount}}</h2>
                                <h3>奖学金</h3>
                            </span>
                        </div>
                    </div>
                    <div v-if="model.type == 1" class="right">
                        <p class="prd_name">@{{model.name}}</p>
                        <p class="price">成交价：￥@{{model.price}}</p>
{{--                        <p class="thisnum">目前{{count($tendering->tender_logs)}}人出价</p>--}}
                    </div>
                    <!-- 暗标图标 -->
                    <span v-if="model.type == 0" class="dmlogo">暗标</span>
                    <span v-if="model.type == 1" class="dmlogo">竞拍</span>
                    </a>
                </div>
                <p v-if="hasMore" v-on:click="more">加载更多</p>
                <p v-if="!hasMore">没有更多了</p>
            </div>
            <!-- 获奖排名 -->
            <div id="award_rankingbox" class="underwaybox award_rankingbox">
                <div class="ranking">
                    @foreach($winners as $i => $winner)
                        @if($i == 0)
                        <div class="first">
                            <img class="firstlogo" src="/tender/images/firstlogo.png" alt="">
                        @elseif($i == 1)
                            <!-- 排名第二 -->
                            <div class="first second">
                                <img class="firstlogo" src="/tender/images/secondlogo.png" alt="">
                        @elseif($i == 2)
                            <!-- 排名第三 -->
                            <div class="first third">
                                <img class="firstlogo" src="/tender/images/thirdlogo.png" alt="">
                        @else
                            <!-- 排名第四 -->
                            <div class="first third">
                                <span class="rankinglogo">{{$i+1}}</span>
                        @endif
                                <img class="usertx" src="{{$winner->wx['headimg']}}" alt="">
                                {{--<span class="phone">{{$winner->wx['headimg']}}</span>--}}
                                <span class="price"><img style="width: 1rem;" src="/tender/images/flower_logo.png">{{$winner->bonus}}</span>
                                <span class="data">{{substr($winner->created_at, 5, 5)}}</span>
                            </div>
                    @endforeach
                </div>
            </div>
            <!-- 无数据时 -->
            <div class="nodata">
                <img src="/tender/images/nodata.png" alt="">
                <p>您还没有任何排名</p>
            </div>
        </div>
    </div>
    <!-- 底部 -->
    <div class="foot-con">
        <div class="foot-nav-list">
            <a href="javascript:;" class="foot-nav index-active">
                <i><img src="/tender/images/index_logo.png" alt=""></i>
                <span>主页</span>
            </a>
            @if(Auth::guard('front')->user())
                <a href="/tender/recharge" id="cart" class="foot-nav shopp">
                    <i><img src="/tender/images/shopp_logoh.png" alt=""></i>
                    <span>充值</span>
                </a>
                <a href="/tender/my" class="foot-nav user">
                    <i><img src="/tender/images/user_logoh.png" alt=""></i>
                    <span>我的</span>
                    <span class="unread-msg-count"></span>
                </a>
            @else
                <a href="{{$loginUrl}}" id="cart" class="foot-nav shopp">
                    <i><img src="/tender/images/shopp_logoh.png" alt=""></i>
                    <span>充值</span>
                </a>
                <a href="{{$loginUrl}}" class="foot-nav user">
                    <i><img src="/tender/images/user_logoh.png" alt=""></i>
                    <span>我的</span>
                    <span class="unread-msg-count"></span>
                </a>
            @endif
            <a href="/tender/about" class="foot-nav about">
                <i><img src="/tender/images/about_logoh.png" alt=""></i>
                <span>关于</span>
            </a>
        </div>
    </div>
</div>
<div id="signUp">
    <div class="sign_card">
        <img src="/tender/images/signup_11.png">
        <img :src="addup >= 2 ? '/tender/images/signup_21.png' : '/tender/images/signup_20.png' ">
        <img :src="addup >= 3 ? '/tender/images/signup_31.png' : '/tender/images/signup_30.png' ">
        <img :src="addup >= 4 ? '/tender/images/signup_41.png' : '/tender/images/signup_40.png' ">
    </div>
    <div class="sign_card">
        <img :src="addup >= 5 ? '/tender/images/signup_51.png' : '/tender/images/signup_50.png' ">
        <img :src="addup >= 6 ? '/tender/images/signup_61.png' : '/tender/images/signup_60.png' ">
        <img :src="addup >= 7 ? '/tender/images/signup_71.png' : '/tender/images/signup_70.png' ">
    </div>
    <div class="sign_gift">
        <img src="/tender/images/signup_gift.png?v=1">
    </div>
    <div class="sign_btn">
        <img src="/tender/images/signup_btn.png">
    </div>
</div>
            @if(Auth::guard('front')->user())
                <input type="hidden" id="hasLoged" value="Y">
            @else
                <input type="hidden" id="hasLoged" value="N">
            @endif

<script src="/tender/js/jquery.min.js"></script>
<script src="/tender/js/swiper.js"></script>
<script src="/tender/js/jquery-weui.min.js"></script>
<script src="/front/js/vue.min.js"></script>
<script src="/tender/js/index.js?v=3"></script>
<script>
$(function () {

    // 调起打卡、1、看本地存储，是否打过；2调取服务器看是否打过
    var k = 'last_signup_date';
    var signup = new Vue({
        el: '#signUp',
        data: {
            addup: 2
        }
    })
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN' : "{{ csrf_token() }}"
        }
    });

    if ($("#hasLoged").val() == "Y" && localStorage.getItem('last_signup_date') != (new Date()).toLocaleDateString()) {
        // 如果今天还未打卡
        $.ajax('/tender/hasSigned', {
            dataType: 'json',
            method: 'GET',
            success: function (res) {
                if (res.data.hasSigned === 0) {
                    signup.addup = res.data.addup
                    $('#signUp').show()
                }
            }
        })
    }

    $('.sign_btn').on('click', function () {
        $('#signUp').hide()
        $.ajax('/tender/signup', {
            method: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.code === 200) {
                    localStorage.setItem('last_signup_date', (new Date()).toLocaleDateString())
                }
            }
        })
    })

    $('.go-detail').on('click', function () {
        location.href = $(this).data('url')
    })

    $(document.body).pullToRefresh().on("pull-to-refresh", function() {
        location.href = location.href
    });

    var swiperPrice = new Swiper('.swiper-price', {
        direction: 'vertical',
        speed: 1000,
        spaceBetween: 1,
        height: 25,
        loop: true,
        autoplay: 1000
    });

})
</script>
@include('front.tender.foot')
