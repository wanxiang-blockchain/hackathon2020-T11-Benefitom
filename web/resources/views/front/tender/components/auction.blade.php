<!-- 竞拍 -->
<div class="dark_mark">
    <div class="left">
        <img src="{{asset('/storage/' . $tendering->banner)}}" alt="">
    </div>
    <div class="right">
        <p class="prd_name">{{$tendering->name}}</p>
        <p class="price">￥{{$tendering->lastPrice()}}</p>
        <p class="thisnum">目前{{count($tendering->tender_logs)}}人出价</p>
    </div>
    <!-- 竞拍图标 -->
    <span class="dmlogo">竞拍</span>
</div>