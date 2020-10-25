@section('title', '账户设置 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo">
                            <h4 class="ui header">基本信息</h4>
                            <div class="ui clearing segment settingPhone">
                                {{--<a href="{{route('member/userinfoEdit')}}" class="ui right floated header">修改</a>--}}
                                <div class="ui left floated header">
                                    <h3 class="ui header">
                                    </h3>
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">手机号</div> 已认证 {!! substr_replace($member->phone,'*****', 3,5) !!}
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">姓名</div> {!! $member->name !!}
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">身份证</div> {!! substr_replace($member->idno,'*****', 6,8) !!}
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">备用手机号</div> {!! $member->sec_phone !!}
                                </div>
                            </div>
                            <h4 class="ui header">收货信息</h4>
                            <div class="ui clearing segment settingPhone">
                                <a href="{{route('addr/index')}}" class="ui right floated header">修改</a>
                                <div class="ui left floated header">
                                    <h3 class="ui header">
                                    </h3>
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">手机号</div> {!! $addr->phone !!}
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">姓名</div> {!! $addr->name !!}
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">省份</div> {!! $addr->province !!}
                                </div>
                                <div class="ui left aligned header">
                                    <div class="ui red horizontal label">地址</div> {!! $addr->city . $addr->area . $addr->addr !!}
                                </div>
                            </div>
                            <h4 class="ui header" style="margin:0;padding-top:0;">密码管理</h4>
                            <div class="ui clearing segment loginPassword">
                                <a href="{{\App\Utils\UrlUtil::flexLoginUrl()}}" class="ui right floated header">修改</a>
                                <div class="ui left floated header">
                                    <h3 class="ui header">
                                        <img src="{{asset('front/image/setting_icon2.png')}}">
                                        <div class="content">登录密码<div class="sub header">登录系统平台时使用</div>
                                        </div>
                                    </h3>
                                </div>
                            </div>
                            <div class="ui clearing segment dealPassword">
                                <a href="{{route('member/resetTradePassword')}}" class="ui right floated header">
                                    @if(empty($trade_pwd))
                                        设置
                                    @else
                                        修改
                                    @endif
                                </a>
                                <div class="ui left floated header">
                                    <h3 class="ui header">
                                        <img src="{{asset('front/image/setting_icon3.png')}}">
                                        <div class="content">交易密码<div class="sub header">投资时的保障</div>
                                        </div>
                                    </h3>
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
