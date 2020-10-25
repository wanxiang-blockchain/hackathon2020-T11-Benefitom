@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/mybill.css?v=1">
</head>
<body >
<div class="wrap">
    <div class="main" id="main-app">
        <div class="nav">
            <img src="/tender/images/flower_logo.png" alt="">
            <h2>流水记录</h2>
        </div>
        <div class="record">
            <ul>
                <li v-for="model in models">
                    <p>@{{ model.desc }}<span>@{{ model.amount }}</span></p>
                    <p class="b">@{{ model.created_at }}</p>
                </li>
            </ul>
        </div>
        <p class="loadmore" v-if="hasMore" v-on:click="more">加载更多</p>
        <p class="loadmore" v-if="!hasMore">没有更多了</p>
    </div>
    {{--<div class="nodata">--}}
        {{--<img src="images/nobilldata.png" alt="">--}}
        {{--<p>您还没有任何流水记录哟～</p>--}}
    {{--</div>--}}
    <!-- 底部 -->
    @include('front.tender.layouts.myfoot')
</div>
<script src="/tender/js/jquery.min.js"></script>
<script src="/tender/js/jquery-weui.min.js"></script>
<script src="/front/js/vue.min.js"></script>
<script>

    var vueApp = new Vue({
        el: '#main-app',
        data: {
            models: [
//                {
//                    desc: '兑换',
//                    amount: 20,
//                    created_at: '2017.10.18 12:34:32'
//                }
            ],
            lastId: 0,
            hasMore: 1
        },
        methods: {
            more: function () {
                fetchGuessedTender(this)
            }
        }
    })

    // 获取拍卖结束的竞拍品
    function fetchGuessedTender(vueApp) {
        $.ajax('/tender/mybills/' + vueApp.lastId, {
            method: 'get',
            dataType: 'json',
            success: function (res) {
                if(res.code == 200){
//                    if(res.data.lastId > 0){
////                        $('.nodata').hide();
//                    }
                    vueApp.lastId  = res.data.lastId
                    vueApp.hasMore = res.data.hasMore
                    vueApp.models = vueApp.models.concat(res.data.models)
                }
            }
        })
    }

    fetchGuessedTender(vueApp)
</script>
@include('front.tender.foot')
