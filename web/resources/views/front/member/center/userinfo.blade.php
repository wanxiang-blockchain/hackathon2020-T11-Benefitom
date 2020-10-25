@section('title', '个人信息 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                        <div class="thirteen wide column article">
                            <div class="articleInfo">
                                <p>姓名：{{$member->name}}</p>
                                <p>身份证号：{{$member->idno}}</p>
                                <p>手机号：{{$member->phone}}</p>
                                <p>备用电话：{{$member->sec_phone}}</p>
                                <p>性别{{$member->sexLabel}}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
