@section('title', '充值成功 - 用户管理中心')
@include("front.layouts.head")

<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
{{--                    @include('front.layouts.leftTree')--}}
                    <div class="thirteen wide column article">
                        <div class="articleInfo rechargeSuccess">
                            <div class="ui text container">
                                <h1 class="ui header">
                                    <img class="ui image" src="{{asset('front/image/success.png')}}">
                                    <div class="content">支付成功</div>
                                </h1>
                                <div class="ts_order_intro">
                                    <p>您支付金额为：{{$amount}}qcash</p>
                                    <span>温馨提示：ARTTBC购买成功，请返回艺行派查看</span>
                                </div>
                                <div class="ui middle aligned grid">
                                    <div class="row">
                                        <div class="center aligned column">
                                          {{--<a href="{{route('member/index')}}" class="ts_commonBtn">返回账户总览</a>--}}
                                            @if(request()->get('back_type') == 'recharge')
                                                <a href="{{route('member/recharge')}}" class="ts_commonBtn">继续购买</a>
                                            @else
                                                <a href="/subscription/detail/{{request()->get('back_type')}}?sub_num={{request()->get('sub_num')}}&has_check={{request()->get('has_check')}}" class="ts_commonBtn">返回</a>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
