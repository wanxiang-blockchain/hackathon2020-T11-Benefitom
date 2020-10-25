@section('title', '用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column userIndex">
                        <div class="ui stackable inverted divided grid">
                            <div class="seven wide column">
                              <div class="ui equal width internally grid changeNoHead">
                                <div class="center aligned row">
                                  <div class="column">
                                    @if(\Auth::guard('front')->check())
                                      <p>您好</p>
                                      <p>{!! Auth::guard('front')->user()->phone!!}</p>
                                    @else
                                      <p>暂未登录</p>
                                    @endif
                                  </div>
                                  <div class="column">
                                    <h3>{{number_format($balance, 2)}}qcash</h3>
                                    <p class="ui header"><img class="ui image" src="{{asset('front/image/total_icon.png')}}">现金账户余额</p>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="six wide column">
                                <div class="balance">
                                    <p style="line-height: 45px;">赠品账户余额： {{number_format($artbc, 2)}} qcash &nbsp;
                                        <a href="/member/artbc/ti"><span class="ui primary button">提取</span></a>
                                    </p>
                                </div>
                            </div>
                            <div class="three wide column">
                                <div class="withdrawal">
                                    <a href="{{route('member/recharge')}}">现金账户充值</a>
                                    <a href="{{route('member/withdraw')}}">现金账户提现</a>
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
