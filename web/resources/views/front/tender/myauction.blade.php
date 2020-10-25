@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/mywatch.css">
</head>
<body >
<div class="wrap">
    <div class="main" id="main-app">
        <div v-for="model in models" class="dark_mark">
            <div class="left">
                <img :src="model.banner" alt="">
            </div>
            <div class="right auction">
                <div v-for="(item, index) in model.list">
                    <div v-if="index == 0">
                        <p class="prd_name">@{{ model.name }}</p>
                        <p class="cp-price">参拍价：<span>@{{ item.price }}个<span>(小红花)</span></span></p>
                        <p class="thisnum">参拍时间：<span>@{{ item.created_at }}</span></p>
                    </div>

                    <!-- 多个 -->
                    <div v-if="index > 0" class="more-data">
                        <p class="cp-price">参拍价：<span>@{{ item.price }}个<span>(小红花)</span></span></p>
                        <p class="thisnum">参拍时间：<span>@{{ item.created_at }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        <p class="loadmore" v-if="hasMore" v-on:click="more">加载更多</p>
        <p class="loadmore"  v-if="!hasMore">没有更多了</p>
        <div class="weui-loadmore weui-loadmore_line no-data">
            <span class="weui-loadmore__tips">暂无数据</span>
        </div>
    </div>
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
//                    banner: '/tender/images/product1.png',
//                    name: '和田白玉籽料红皮一路连科挂件',
//                    list: [
//                        {
//                            price: 11,
//                            created_at: '2017.10.18 12:34:32'
//                        },
//                        {
//                            price: 13,
//                            created_at: '2017.10.18 12:34:32'
//                        }
//                    ]
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
        $.ajax('/tender/myauctions/' + vueApp.lastId, {
            method: 'get',
            dataType: 'json',
            success: function (res) {
                if(res.code == 200){
                    if(res.data.lastId > 0){
                        $('.no-data').hide();
                    }
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
