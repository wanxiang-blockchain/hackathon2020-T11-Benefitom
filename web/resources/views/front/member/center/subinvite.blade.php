@section('title', '邀请记录 - 用户管理中心')
@include("front.layouts.head")
<style>
    .qrcode{
        display: flex;
        flex-direction:column;
        justify-content: center;
        align-items: center;
    }
    .qrcode p{
        margin-top: 13px;
    }
</style>
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="ui stackable inverted equal height stackable grid">
                @include('front.layouts.leftTree')
                <div class="article">

                    <div class="articleInfo">
                        @if(!empty($err))
                            <h4 class="ui header">{{$err}}</h4>
                        @else
                            <h4 class="ui header">{{$subMember->phone}}的邀请人数：{{$invite_number}}</h4>
                            <h4 class="ui header">邀请记录</h4>
                            <table class="ui very basic celled table">
                                <tbody>
                                <tr>
                                    <td class="center aligned">用户名</td>
                                    <td class="center aligned">注册时间</td>
                                </tr>
                                @foreach($list as $item)
                                <tr>
                                    <td class="center aligned">{{$item->phone}}</td>
                                    <td class="center aligned">{{$item->created_at}}</td>
                                </tr>
                                @endforeach
                                </tbody>
                                {{$list->links()}}
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
