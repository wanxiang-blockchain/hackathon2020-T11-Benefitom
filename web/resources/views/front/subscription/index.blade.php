@section('title', '益通云认购中心')
@include('front.layouts.head')
<div class="indexMain pusher">
    <div class="ui container">
        <img class="ui centered medium image" src="/front/image/projectTitle1_1.png">
        <div class="ui items">
            @foreach($data as $k=>$project)
                <div class="item"  data-url="{{route("subscription/detail",['id' => $project->id])}}" onclick="href(this)">
                    <div class="image">
                        <img src="{{asset('storage/'.$project->picture)}}">
                    </div>
                    <div class="content">
                        @if(date('Y-m-d H:i:s',time()) < $project->start)
                            <a href="{{route("subscription/detail",['id' => $project->id])}}" class="projectDetailBtn unfinished enable_pay">未开始</a>
                        @else
                            @if($project->limit == $project->position && date('Y-m-d H:i:s',time()) < $project->end)
                                <a href="{{route("subscription/detail",['id' => $project->id])}}" class="projectDetailBtn finished">已售完</a>
                            @elseif(date('Y-m-d H:i:s',time()) >= $project->end)
                                <a href="{{route("trade/detail",['id' => $project->assetType->id])}}" class="projectDetailBtn unfinished">去买入</a>
                            @else
                                <a href="{{route("subscription/detail",['id' => $project->id])}}" class="projectDetailBtn unfinished">认购中</a>
                            @endif
                        @endif
                        <a class="header showPopup" data-variation="very wide">{{$project->name}}</a>
                        <div class="ui equal width stackable internally celled grid">
                            <div class="aligned row">
                                <div class="column">
                                    <p>Token符号：{{$project->asset_code}}</p>
                                    <p>发行数量：{{$project->total . $project->price_unit}}</p>
                                    <p>专营机构：{{$project->agent}}</p>
                                </div>
                                <div class="column">
                                    <p>单价：{{$project->price}}qcash</p>
                                    <p>单客限购：{{$project->per_limit . $project->price_unit}}/人</p>
                                </div>
                                <div class="column">发行进度</div>
                            </div>
                        </div>
                        <div class="progressCon">
                            <div class="ui progress" data-percent="50">
                                <div class="bar" style="transition-duration:300ms;width:{{$project->progress}}%;"></div>
                            </div>
                            <span>{{$project->progress}}%</span>
                            <div>

                            </div>
                        </div>
                      <div class="extra">
                          <div class="ui time" time="{{strtotime($project->end)}}">
                              <p>下架时间<br>
                                  <span id="t_d">{{$project->end}}</span>
                              </p>
                          </div>
                          <div class="ui">
                              <p>累发数量：{{$project->sold()}}</p>
                              <p>累认次数：{{$project->orderCount}}</p>
                          </div>
                      </div>
                    </div>
                </div>
            @endforeach
                {{$data->links()}}
        </div>
    </div>
</div>

@include('front.layouts.foot');
<script>
//  $('.showPopup').popup();
//   function href(obj){
//       var  url = $(obj).data('url');
//       location.href=url;
//   }
//    function GetRTime(){
//        $('.time').each(function(k, obj){
//            if(!$(obj).attr('time')) {
//                return false;
//            }
//            var ends = $(obj).attr('time')*1000;
//            var EndTime= new Date(ends);
//            var NowTime = new Date();
//            var t =EndTime.getTime() - NowTime.getTime();
//            var d=Math.floor(t/1000/60/60/24);
//            var h=Math.floor(t/1000/60/60%24);
//            var m=Math.floor(t/1000/60%60);
//            var s=Math.floor(t/1000%60);
//            $(obj).find('p').find('span').html(d + "天" + h + "时" + m + "分" + s + "秒");
//        });
//    }
//    setInterval("GetRTime()",1000);
</script>
