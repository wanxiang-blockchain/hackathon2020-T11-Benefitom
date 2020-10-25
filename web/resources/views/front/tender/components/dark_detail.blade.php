<?php
/**
 * 分宣讲 -》 估价 -》 暗拍 -》 结束
 */
?>
<div class="section">
    <div class="name_info">
        <span class="name">{{$model->name}}</span>
        <span class="info">{{$model->process()}}</span>
    </div>
    <div class="name_info">
        <span class="name">估值</span>
        <span class="info">{{$model->valuation}}</span>
    </div>
    <div class="name_info">
        <span class="name">{{"估价时段："}}</span>
        <span class="info">{{$model->guessTime()}}</span>
    </div>
    <div class="name_info">
        <span class="name">{{"出价时段："}}</span>
        <span class="info">{{$model->tenderTime()}}</span>
    </div>
    <div class="name_info">
        <span class="name">逛逛<a style="color: indianred;" href="/"> 益通云</a> 或 <a style="color: indianred;" href="{{\App\Utils\UrlUtil::flextHuiUrl()}}">易宝堂</a></span>
    </div>
    <div class="pro-price">
        目前累计奖学金：
        <span class="price">{{$model->priceCount()}}</span>
        {{--<span class="time">2017.10.19 12:32:24</span>--}}
    </div>
</div>
<!-- 火眼金睛 ，组队奖-->
<div class="section3">
    <div class="prize">
        <div class="group eye show_rule1"><a style="color: indianred;" href="javascript:;" >火眼金睛奖</a> <span>奖品：<span>1人享有{{$model->firstPrice()}}(小红花)</span></span></div>
        <div class="group show_rule2"><a style="color: indianred;" href="javascript:;" >金睛奖</a> <span>奖品：<span>{{$model->secondPriceMembers()}}人瓜分{{$model->secondPrice()}}(小红花)</span></span></div>
    </div>
</div>
@if(\App\Utils\DateUtil::now() > $model->guess_start)
<div class="section3">
    <div class="prize">
        <div class="operation">
            <div class="myguess">我的估价</div>
        </div>
        @foreach($myguesses as $guess)
            <div class="group">{{$guess->created_at}}<span>{{$guess->tender_price}}qcash</span></div>
        @endforeach
    </div>
</div>
@endif
@if(\App\Utils\DateUtil::now() > $model->tender_start)
    <div class="section3">
        <div class="prize">
            <div class="group eye">我的出价 <span><span>{{empty($mytenders) ? '暂无' : $mytenders->price}}</span></span></div>
        </div>
    </div>
@endif