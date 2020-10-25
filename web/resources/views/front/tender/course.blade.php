@include('front.tender.head')
<style>
    .main{
        background: #fff;
    }
    .course-item{
        width: 100%;
        padding: 5px 12px 5px 12px;
        /*margin-top: 13px;*/
        display: inline-block;
    }
    .left{
        width: 25%;
    }
    .left img{
        width: 100%;
        float: left;
    }
    .right{
        width: 70%;
        float: left;
        margin-left: 5%;
    }
</style>
</head>
<body ontouchstart>

<div class="wrap">
    <div class="main">
        <!-- 已经结束 -->
        <div>
            @foreach($models as $model)
                <div class="course-item">
                    <a href="/tender/course/detail/{{$model->id}}">
                        <div class="left">
                            <img src="{{$model->poster}}" alt="">
                        </div>
                        <div class="right">
                            <p class="prd_name">{{$model->name}}</p>
                            <p class="prd_name">{{$model->summary}}</p>
                            <p class="price">{{$model->created_at}}</p>
                        </div>
                    </a>
                </div>
            @endforeach
            {{--<div v-for="model in models" class="dark_mark">--}}
                {{--<a :href="'/tender/detail/' + model.id">--}}
                    {{--<div class="left">--}}
                        {{--<img alt="">--}}
                    {{--</div>--}}
                    {{--<div class="right">--}}
                        {{--<p class="prd_name">@{{model.name}}</p>--}}
                        {{--<p class="price">成交价：￥@{{model.price}}</p>--}}
                    {{--</div>--}}
                {{--</a>--}}
            {{--</div>--}}
            {{--<p v-if="hasMore" v-on:click="more">加载更多</p>--}}
            {{--<p v-if="!hasMore">没有更多了</p>--}}
        </div>
    </div>
    <script src="/tender/js/jquery.min.js"></script>
    <script src="/tender/js/jquery-weui.min.js"></script>
    <script src="/front/js/vue.min.js"></script>
    <script>

    </script>

@include('front.tender.foot')
