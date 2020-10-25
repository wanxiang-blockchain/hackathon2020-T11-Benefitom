@section('title', '认购结果')
@include("front.layouts.head")
<div class="orderConfirm pusher">
    <div class="ui container">
        <div class="ts_success">
            <div class="ui breadcrumb">
                <a class="section" href="/subscriber/itemsList.shtml">认购中心</a>
                <i class="right angle icon divider"></i>
                <a class="section" href="/subscriber/details.shtml">认购详情</a>
                <i class="right angle icon divider"></i>
                @if(request()->get('type') == 1)
                    <div class="active section">认购成功</div>
                @else
                    <div class="active section">认购失败</div>
                @endif
            </div>
            <div class="ui text container">
                <h2 class="ui header">
                    @if(request()->get('type') == 1)
                        <img class="ui image" src="{{asset('front/image/success.png')}}">
                        <div class="content">认购成功</div>
                        @else
                        <img class="ui image" src="{{asset('front/image/failed.png')}}">
                        <div class="content">认购失败</div>
                    @endif
                </h2>
                <div class="ts_order_intro">
                    <p>项目名称: <span>{{request()->get('name')}}</span></p>
                    <p>认购份数：<span>{{request()->get('num')}}</span>份</p>
                    <p>认购金额：<span>{{request()->get('amount')}}</span>qcash</p>
                    <p>备注：<span>{{request()->get('note')}}</span></p>
                </div>
                <div class="ui middle aligned grid">
                  <div class="row first">
                    <div class="center aligned column">
                      <a style="color: #fff;background: #cba078" class="ts_commonBtn" href="/subscription/detail/{{request()->get('id')}}">继续认购</a>
                    </div>
                  </div>
                  <div class="row last">
                    <div class="center aligned column">
                      <a style="color: #cba078" href="{{route('member/index')}}">可去往用户管理中心</a>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
