@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/product-details.css">
</head>
<body ontouchstart>

<div class="weui-pull-to-refresh__layer">
    <div class='weui-pull-to-refresh__arrow'></div>
    <div class='weui-pull-to-refresh__preloader'></div>
    <div class="down">下拉刷新</div>
    <div class="up">释放刷新</div>
    <div class="refresh">正在刷新</div>
</div>
<div class="wrap">
    <div class="main">
        <div style="width:100%;">
            <video width="100%" src="{{$model->video}}" controls poster="{{asset('/storage/' . $model['poster'])}}">
                Sorry, your browser doesn't support embedded videos,
                but don't worry, you can <a href="videofile.webm">download it</a>
                and watch it with your favorite video player!
            </video>
        </div>
        @if($model->isDark())
            @include('front.tender.components.dark_detail')
        @else
            @include('front.tender.components.auction_detail')
        @endif

        @if($model->tenderEnded())
            <div class="section3">
                <div class="prize">
                    <div class="group eye">成交价 <span><span>{{$model->dealPrice()}}qcash</span></span></div>
                </div>
            </div>
        @endif

        <div class="section2">
            <div class="details">
                <span class="active">图文详情</span>
                {{--<span>商品参数</span>--}}
            </div>
            <!-- 详情图 -->
            <div class="detailsimg">
                {!! $model->info !!}
            </div>
        </div>
    </div>

    <!-- 分享，我要参与 ，出价， 我要投标 ，我要估价-->
    <div class="foot-box">
        <div class="footer">
            {{--<a class="fx" href="javascript:;">点右上角分享</a>--}}
            {{--<a class="cy" href="#">我要参与</a>--}}
            {{--<!--<a class="cy" href="#">出价</a>-->--}}
            @if($model->isGuessing())
                <a class="tb" data-cmd="guess" href="javascript:;">我要估价</a>
            @elseif($model->goingTender())
                <a class="fx" data-cmd="guess" href="javascript:;">竞拍即将开始</a>
            @elseif($model->isTendering())
                <a class="tb" data-cmd="tender" href="javascript:;">我要出价</a>
            @elseif(\App\Utils\DateUtil::now() > $model->tender_end)
                <a class="fx" href="javascript:;">拍卖结束</a>
            @endif
            <input type="hidden" id="tender_id" value="{{$model->id}}" />
            @if($model->goingTender())
                <div class="endtimebg"></div>
                <div class="endtime">
                    <input type="hidden" id="tender_start" value="{{strtotime($model->tender_start)}}">
                    {{--<span class="rpz">热拍中</span>--}}
                    <input type="hidden" id="start_end" value="{{strtotime($model->tender_end)}}" />
                    <span class="time">距竞拍开始：<span class="todo-hour"></span>时<span class="todo-min"></span>分<span class="todo-sec"></span>秒</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 账户余额不足 -->
    <div class="fixbg"></div>
    <div class="noprice">
        <img class="zerologo" src="/tender/images/zerologo.png" alt="">
        <p>您的账户余额不足呦~</p>
        <a class="recharge" href="#">立即充值</a>
    </div>
</div>
@if(Auth::guard('front')->user())
    <input type="hidden" id="hasLoged" value="Y">
@else
    <input type="hidden" id="hasLoged" value="N">
@endif
<script src="/tender/js/jquery.min.js"></script>
<script src="/tender/js/jquery-weui.min.js"></script>
<script src="/tender/js/product-details.js"></script>
<script>

    var timecountdown
    function countdown() {

        var val = $(".todo-hour")
        if(!val) {
            return false;
        }
        var tender_start = $('#tender_start').val()
        if(!tender_start){
            return false
        }

        var now = new Date();

        var leftTime = now.getTime();
        var leftsecond = tender_start - parseInt(leftTime/1000);

        var hour = parseInt(leftsecond / 3600);
        var minute = parseInt((leftsecond - hour * 3600 )/ 60);
        var sec = parseInt(leftsecond - hour *3600 - minute * 60);

        console.log('tender_start:' + tender_start)

        $(val).html('<b>' + hour + '</b>')
        $(val).parent().find('.todo-min').html('<b>' + minute + '</b>')
        $(val).parent().find('.todo-sec').html('<b>' + sec + '</b>')
        timecountdown = setTimeout("countdown()", 1000)
    }

    $(function () {

        countdown();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : "{{ csrf_token() }}"
            }
        });
        $('.tb').on('click', function () {
            // 先判断登录没有
            if($("#hasLoged").val() != "Y"){
                $.alert('请先进行登录或注册', function () {
                    location.href = "{{\App\Service\SsoService::fetchHost() . '/mobile/index.html?appid=5a88e8dedc1ecf7fb04a074bdea376cf&returnurl=' . route('tender')}}"
                })
                return false
            }
            var tender_id = $('#tender_id').val()
            var cmd = $(this).data('cmd')
            var text = cmd == 'guess' ? "每估价一次，花费10朵小红花" : ''
            var title = cmd == 'guess' ? '输入你的估价' : "请输入你的出价"
            var _this = $(this)
            //如果参数过多，建议通过 object 方式传入
            $.prompt({
                text: text,
                title: title,
                onOK: function(price) {
                    if(!/^[0-9]+$/.test(price)) {
                        $.alert("价格必须为整数");
                        return;
                    }
//                    _this.removeClass('tb')
                    $.showLoading()
                    // 花费小红花，进行估价
                    $.ajax('/tender/' + cmd,{
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            tender_id: tender_id,
                            price: price
                        },
                        success: function (res) {
                            $.hideLoading()
                            if(res.code == 200) {
                                $.alert("操作成功", function () {
                                    location.href = location.href
                                });
                            }else if(res.code == 208) {
                                $.alert(res.data, function () {
                                    location.href = '/tender/margin'
                                });
                            }else if (res.code == 210) {
                                $.alert(res.data, function () {
                                    location.href = '/tender/contract'
                                });
                            }else {
                                $.alert(res.data, function () {
                                    location.href = location.href
                                })
                            }
                        },error: function (err) {
                            $.alert(err, function () {
                                location.href = location.href
                            })
                        }
                    })
                },
                onCancel: function() {
                    console.log("取消了");
                },
                input: ''
            });
        })

        $(".show_rule1").on('click', function () {
            // 显示火眼金睛奖规则
            $.alert("<p style='line-height: 25px;'>1、取估价阶段估价价离成交价最近且最先出价一次作为火眼金睛奖<br>2、取估价环节参与者30%份小红花作为火眼金睛奖红包</p>", "火眼金睛奖规则");
        })
        $(".show_rule2").on('click', function () {
            // 显示金睛奖规则
            $.alert("<p style='line-height: 25px;'>1、取估价阶段估价价离成交价最近且最先出价前10%作为金睛奖<br>2、取估价环节参与者50%份小红花作为金睛奖红包平分</p>", "金睛奖规则");
        })
    })
    $(document.body).pullToRefresh().on("pull-to-refresh", function() {
        location.href = location.href
    });
</script>

@include('front.tender.foot')
