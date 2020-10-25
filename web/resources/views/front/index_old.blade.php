@ilsection('title', '绍德艺品易货中心')
@include("front.layouts.head")
<script type="text/javascript" src="js/echarts-all-3.js"></script>

<div class="pusher">
    @if (count($banners)>0)
    <div class="slider">
        @if(!Auth::guard('front')->check())
        <div class="ui container fastLogin">
        </div>
        @endif
        <ul class="slider-content">
            @foreach($banners as $banner)
            <li class="slider-item @if(!$loop->index) active @endif" >
                <a class="a-img-bg" href="{{$banner->link}}" target="_blank"
                   style="background-image: url('{{asset('storage/'.$banner->url)}}'); background-repeat:  no-repeat;background-position: center;
                           /*filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../image/banner.jpg', sizingMethod='scale')\9;*/
                           height: 100%"></a>
            </li>
            @endforeach
        </ul>
        <ol class="slider-indicator">
            @foreach($banners as $banner)
            <li></li>
            @endforeach
        </ol>
        <a class="slider-left-control" href="javascript:void(0)"><i class="icon chevron left"></i></a>
        <a class="slider-right-control" href="javascript:void(0)"><i class="icon chevron right"></i></a>
    </div>
   @endif
    @if(!empty($notice['0']))
     <div class="notice aligned">
        <div class="ui container">
            <div class="ui list">
                <div class="item">
                    <i class="volume up icon"></i>
                    <div class="content noticeRoll">
                        <ul>
                            @foreach($notice as $value)
                            <li><a href="{{route('about/notice',['id'=>$value->id])}}">{{$value->title}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="noticeMore">
                        <a href="{{route("about/notice")}}">更多</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="indexMain">
        <div class="ui container project">
             <img class="ui centered medium image" src="/front/image/projectTitle1.png">
            <div class="ui items">
                @foreach($projects as $k=>$project)
                        <div class="item" data-url="{{route("subscription/detail",['id' => $project->id])}}" onclick="href(this)">
                            <div class="image">
                                <img src="{{asset('storage/'.$project->picture)}}">
                            </div>
                            <div class="content">
                                @if(date('Y-m-d H:i:s',time()) < $project->start)
                                    <a href="{{route("subscription/detail",['id' => $project->id])}}" class="projectDetailBtn unfinished enable_pay">未开始</a>
                                @else
                                    @if($project->hasSellOut() && date('Y-m-d H:i:s',time()) < $project->end)
                                        <a href="{{route("subscription/detail",['id' => $project->id])}}" class="projectDetailBtn finished">已售完</a>
                                    @elseif(date('Y-m-d H:i:s',time()) >= $project->end)
                                        <a href="{{route("trade/detail",['id' => $project->assetType->id])}}" class="projectDetailBtn unfinished">去买入</a>
                                    @else
                                        <a href="{{route("subscription/detail",['id' => $project->id])}}" class="projectDetailBtn unfinished">认购中</a>
                                    @endif
                                @endif
                                <a class="header showPopup" data-html="" data-variation="very wide">{{$project->name}}</a>
                                <div class="ui equal width stackable internally celled grid">
                                    <div class="aligned row">
                                        <div class="column">
                                            <p>Token符号：{{$project->asset_code}}</p>
                                            <p>发行数量：{{$project->total . $project->price_unit}}</p>
                                            <p>认购额度：{{$project->per_limit . $project->price_unit}}/人</p>
                                        </div>
                                        <div class="column">
                                            <p>专营机构：{{$project->agent}}</p>
                                            <p>提货规则：{{$project->rule . $project->rule_desc}}</p>
                                            <p>认购价格：{{$project->price }} qcash/{{$project->price_unit}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="progressCon">
                                    <div class="ui progress" data-percent="50">
                                        <div class="bar" style="transition-duration:300ms;width:{{$project->progress}}%;"></div>
                                    </div>
                                    <span>{{$project->progress}}%</span>
                                </div>
                                <div class="extra">
                                    <div class="ui right floated">
                                        <p>认购次数<br/>{{$project->ordercount}}</p>
                                    </div>
                                    @if(date('Y-m-d H:i:s',time()) < $project->start)
                                        <div class="ui time">
                                            <p>活动时间为<br>
                                                <span id="t_d">{{date('Y-m-d H:i:s',strtotime($project->start))}} 至 {{date('Y-m-d H:i:s',strtotime($project->end))}}</span>
                                            </p>
                                        </div>
                                     @elseif($project->limit == $project->position || date('Y-m-d H:i:s',time()) > $project->end)
                                        <div class="ui time" >
                                            <p>抢购剩余时间<br>
                                                <span >00天00时00分00秒</span>
                                            </p>
                                        </div>
                                     @else
                                        <div class="ui time" id="times" time="{{strtotime($project->end)}}">
                                            <p>抢购剩余时间<br>
                                                <span id="t_d">00天00时00分00秒</span>
                                            </p>
                                        </div>
                                     @endif
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
            @if($num > 3)
            <div class="projectMore">
                <a href="/subscription" class="ui button basic">查看更多</a>
            </div>
            @endif
        </div>

        <div class="ui container introduction">
          <img class="ui centered medium image" src="/front/image//projectTitle4.png">
          <div class="ui two stackable doubling cards">
            <a class="ui card" href="{{route('about/company')}}">
              <img class="ui fluid image" src="/front/image/introduce_pic1.png">
              <div class="ui horizontal divider">
                益通云介绍
              </div>
            </a>
            <a class="ui card" href="{{route('artists')}}">
              <img class="ui fluid image" src="/front/image/introduce_pic2.jpg">
              <div class="ui horizontal divider">
                链英科技介绍
              </div>
            </a>
          </div>
        </div>
        <div class="ui container tradeCenter">
          {{--<img class="ui centered medium image" src="/front/image/projectTitle5.png">--}}
            {{--@include('front.layouts.kkChart_dev')--}}
          {{--<img class="ui fluid image" src="/front/image//trade.jpg">--}}
        </div>
        <div class="ui container news">
            <div class="ui three stackable doubling cards">
                @foreach($category as $c)
                <div class="ui card">
                    <h3>{{$c->name}}</h3>
                    <div class="image">
{{--                        <img  src="{!! asset('storage/'.$c['pictures'][0]['url']) !!}"  alt="">--}}
                    </div>
                    <div class="content">
                        <div class="ui middle aligned list">
                            @foreach($c->articles as $a)
                                @if($loop->index>2)
                                    @break;
                                @endif
                            <a class="item" href="{{route($c->href,['id' => $a->id])}}">
                                <div class="right floated content">{{date('Y-m-d',strtotime($a->created_at))}}</div>
                                <div class="content">{{$a->title}}</div>
                            </a>
                            @endforeach
                            @if(count($c->articles)>3)
                            <div class="item">
                                <div class="right floated content">
                                    <a href="{{route($c->href)}}"><<查看更多</a>
                                </div>
                            </div>
                              @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="tszy_toolbar right menu">
  <a class="tszy_toolbar_tab toolbar_tab_top" href="javascript:;">
    <img class="iconImg" src="/front/image//fixed_icon4.png">
    返回顶部
  </a>
</div>
@include("front.layouts.foot")
<script type="text/javascript">
  $('.showPopup').popup();
   function href(obj){
     var  url = $(obj).data('url');
          location.href=url;
   }
    function GetRTime(){
        $('.time').each(function(k, obj){
            if(!$(obj).attr('time')) {
                return false;
            }
            var ends = $(obj).attr('time')*1000;
            //var EndTime= new Date(ends);
            var NowTime = new Date();
            var t =ends - NowTime.getTime();
            var d=Math.floor(t/1000/60/60/24);
            var h=Math.floor(t/1000/60/60%24);
            var m=Math.floor(t/1000/60%60);
            var s=Math.floor(t/1000%60);
            $(obj).find('p').find('span').html(d + "天" + h + "时" + m + "分" + s + "秒");
        });
    }
    setInterval("GetRTime()",1000);
    $(".floatBox .close").click(function(){
      $(".floatBox").css({"display":"none"});
    })
    $(".toolbar_tab_top").click(function(){
      $("html,body").animate({scrollTop:0},500);
    })
    $(function () {
        $('form').on('submit', function(){
            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#password').val();
            var enc = crypt.encrypt(old);
            $('#password').val(enc);

            if(!$('#phone').val())
            {
                msg_error('请输入正确的手机号', 'phone');
                $('#password').val(old);
                return false;
            }
            if(!$('#password').val())
            {
                msg_error('请输入密码', 'password');
                $('#password').val(old);
                return false;
            }
            var form = $('form').serialize();
            $('#password').val(old);
            $.post('login', form, function(res){
                if(res.code != 200) {
                    msg_error(res.data);
                } else {
                    setTimeout(function(){
                        location.href = '/logSuccess';
                    }, 1000);
                }
            });
            return false;
        })
    })
</script>
