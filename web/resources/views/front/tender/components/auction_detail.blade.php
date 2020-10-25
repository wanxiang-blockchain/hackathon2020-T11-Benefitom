<?php
/**
 * 分宣讲 -》竞拍 -》 结束
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
        <span class="name">{{"竞拍时段："}}</span>
        <span class="info">{{$model->tenderTime()}}</span>
    </div>
    <div class="name_info">
        <span class="name">逛逛<a style="color: indianred;" href="/"> 益通云</a> 或 <a style="color: indianred;" href="{{\App\Utils\UrlUtil::flextHuiUrl()}}">易宝堂</a></span>
    </div>
    <div class="pro-price">
        目前最高价格：
        <span class="price">{{$lastTender ? $lastTender->price : $model->starting_price}}</span>
        <span class="time">{{$lastTender ? $lastTender->created_at : $model->tender_start}}</span>
    </div>
</div>
@if(\App\Utils\DateUtil::now() > $model->tender_start)
    <div class="section3">
        <div class="prize">
            <div class="group eye">我的出价</div>
            @foreach($mytenders as $mytender)
                <div class="group"><span>{{$mytender->price}}qcash</span>{{$mytender->created_at}}</div>
            @endforeach
        </div>
    </div>
@endif