@section('title', '重置交易密码成功 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo resetTradePasswordSuccess">
                            <div class="ui text container">
                                <h1 class="ui header">
                                    <img class="ui image" src="{{asset('front/image/success.png')}}">
                                    <div class="content">修改完成</div>
                                </h1>
                                <div class="ts_order_intro">
                                    <p>交易密码修改成功，亲，请牢记并妥善保存好哦~</p>
                                </div>
                                <div class="ui middle aligned grid">
                                    <div class="row">
                                        <div class="center aligned column">
                                            @if(request()->get('type'))
                                                <a href="/subscription/detail/{{request()->get('type')}}" class="ts_commonBtn">返回</a>
                                            @else
                                                <a href="javascript:;" class="ts_commonBtn" onclick="history.go(-1);">返回</a>
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
