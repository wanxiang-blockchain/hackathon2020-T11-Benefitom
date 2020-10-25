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
                        <h4 class="ui header">邀请码：</h4>
                        <div class="item image qrcode">
                            <img src="{{$qrcode}}" />
                            <p>
                                @if (is_weixin())
                                    长按保存二维码
                                @else
                                    <a download="invite.png" href="{{$qrcode}}">保存邀请码</a>
                                @endif
                            </p>
                            <div class="ui form">
                                <div class="field">
                                    <label><button onclick="copy_link()" class="ui google plus button"> 选中邀请链接 </button></label>
                                    <textarea id="copy-link" rows="2" cols="100" onchange="invite_link_init()">{{route('getRegister') . '?invite_member=' . $member['invite_code']}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 21px;">
                            <h4 class="ui header">我的邀请人数：</h4>
                            <p style="border-bottom: 1px solid #f1c40f;">
                                A级：{{$invite_number}}
                            </p>
                            <p style="border-bottom: 1px solid #f1c40f;">
                                B级：{{$sub_invite_number[0]->number}}
                            </p>
                        </div>
                        <h4 class="ui header">邀请记录</h4>
                        <div class="flex_table">
                            <table class="ui very basic celled">
                                <tbody>
                                <tr>
                                    <td class="center aligned">用户名</td>
                                    <td class="center aligned">手机号</td>
                                    <td class="center aligned">注册时间</td>
                                </tr>
                                @foreach($list as $item)
                                    <tr>
                                        <td class="center aligned"><a href="/member/subinvite/{{$item->phone}}">{{$item->name}}</a></td>
                                        <td class="center aligned"><a href="/member/subinvite/{{$item->phone}}">{{$item->phone}}</a></td>
                                        <td class="center aligned">{{$item->created_at}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                {{$list->links()}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function copy_link() {
//        var link = document.getElementById('copy-link')
//        link.setSelectionRange(0, 9999)
        var myelement = document.getElementById('copy-link'),
            range = document.createRange();

        range.selectNode(myelement);
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range);
    }

    function invite_link_init() {
        var link = document.getElementById('copy-link')
        link.value = "{{route('member/invite') . '?invite_member=' . $member['phone']}}"
    }
</script>
@include("front.layouts.foot")
