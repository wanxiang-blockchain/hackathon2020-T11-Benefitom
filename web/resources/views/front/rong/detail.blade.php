@section('title', '艺融宝')
@include("front.rong.head")

<style>
    .productDetail {
        width: 100%;
        margin-top: 0;
    }
    .productDetail .padd-left{
        padding-left: 10px;
    }
    .go_buy{
        width: 100%;
        display: flex;
    }
    .productDetail .info{
        padding: 20px 15px 20px 15px;
    }
</style>

<div class="productDetail rong">
    <img src="/rong/image/pro_info.jpg">
    <div class="info">
        {!! $model->info !!}
    </div>
   <div class="go_buy">
       <a href="{{'/rong/buy/' . $model->id}}"><img src="/rong/image/go_buy.jpg"></a>
   </div>
</div>
<script>

</script>

@include("front.rong.foot")
