@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/mywatch.css">
</head>
<body >
<div class="wrap">
    <div class="main" id="main-app">

        @foreach($models as $model)
        <div class="dark_mark" data-type="{{$model->type}}" data-id="{{$model->id}}">
            <p class="msg-title">
                {{$model->title}}
                @if($model->has_read == 0)
                    <span>未读</span>
                @endif
            </p>
            <p class="msg-con">{{$model->temp()}}</p>
            <p class="msg-created">{{$model->created_at}}</p>
        </div>
        @endforeach
        <input type="hidden" id="initLastId" value="{{$lastId}}">
        <input type="hidden" id="initHasMore" value="{{$hasMore}}">
        <div v-for="model in models">
            <div class="dark_mark" data-type="@{{ model.type }}" data-id="@{{ model.id }}">
                <p class="msg-title">
                    @{{model.title}}
                    <span v-if="model.has_read == 0">未读</span>
                </p>
                <p class="msg-con">@{{model.con}}</p>
                <p class="msg-created">@{{model.created_at}}</p>
            </div>
        </div>
        <p class="loadmore" v-if="hasMore" v-on:click="more">加载更多</p>
        <p class="loadmore"  v-if="!hasMore">没有更多了</p>
        {{--<div class="weui-loadmore weui-loadmore_line no-data">--}}
            {{--<span class="weui-loadmore__tips">暂无数据</span>--}}
        {{--</div>--}}
    </div>
    <!-- 底部 -->
    @include('front.tender.layouts.myfoot')
</div>
<script src="/tender/js/jquery.min.js"></script>
<script src="/tender/js/jquery-weui.min.js"></script>
<script src="/front/js/vue.min.js"></script>
<script>
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN' : "{{ csrf_token() }}"
        }
    });
    $('.dark_mark').on('click', function () {
        var type = $(this).data('type')
        var id = $(this).data('id')
        $.ajax('/tender/msgRead/' + id,{
            dataType: 'json',
            method: 'POST',
            success: function (res) {
                if(type != 1 && type != 2){
                    location.href = location.href
                }
            }
        })
        if(type == 1){
            location.href = '/tender/mybill'
        }else if (type == 2) {
            location.href = '/tender/myauction'
        }
    })
})
    var vueApp = new Vue({
        el: '#main-app',
        data: {
            models: [
            ],
            lastId: $('#initLastId').val(),
            hasMore: $('#initHasMore').val()
        },
        methods: {
            more: function () {
                fetchMyMsgs(this)
            }
        }
    })

    // 获取拍卖结束的竞拍品
    function fetchMyMsgs(vueApp) {
        $.ajax('/tender/mymsgs', {
            method: 'get',
            data: { lastId: vueApp.lastId },
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

    fetchMyMsgs(vueApp)
</script>
@include('front.tender.foot')
