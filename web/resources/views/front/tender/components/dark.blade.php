<!-- 暗标 -->
<div class="dark_mark">
    <div class="left">
        <img src="{{asset('/storage/' . $tendering->banner)}}" alt="">
    </div>
    <div class="right">
        <p class="prd_name">{{$tendering->name}}</p>
        <div class='number'>
                                <span class="auction_num">
                                    <h2>{{$tendering->tender_end}}</h2>
                                    <h3>开奖时间</h3>
                                </span>
            <span class="win_num">
                                      <h2>{{$tendering->priceCount()}}</h2>
                                    <h3>奖学金</h3>
                                </span>
        </div>
    </div>
    <!-- 暗标图标 -->
    @if($tendering->isGuessing())
        <span class="dmlogo guessinglogo">估价</span>
    @else
        <span class="dmlogo">暗标</span>
    @endif
</div>