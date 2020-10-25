@section('title', '订货通览 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo">
                            <h4 class="ui header">待提货订单列表</h4>
                            <div class="flex_table">
                                <table class="ui very basic celled">
                                    <tbody>
                                    <tr>
                                        <td class="center aligned">提货</td>
                                        <td class="center aligned">楼盘名称</td>
                                        <td class="center aligned">数量</td>
                                    </tr>
                                    @foreach($assets as $asset)
                                        <tr>
                                            <td class="center aligned">
                                                @if($asset->trade_amount > 0)
                                                    <a class="positive ui button" href="/member/delivery/{{$asset->id}}">提货</a>
                                                @else
                                                @endif
                                            </td>
                                            <td class="center aligned">{{$asset->asset_name}}</td>
                                            <td class="center aligned">
                                                {{intval($asset->trade_amount)}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$assets->links()}}
                            <h4 class="ui header">已提货订单列表</h4>
                            <div class="flex_table">
                                <table class="ui very basic celled">
                                    <tbody>
                                    <tr>
                                        <td class="center aligned">楼盘名称</td>
                                        <td class="center aligned">状态</td>
                                        <td class="center aligned">数量</td>
                                        <td class="center aligned">下单时间</td>
                                        <td class="center aligned">收货人</td>
                                        <td class="center aligned">收货人手机号</td>
                                        <td class="center aligned">收货人地址</td>
                                        {{--<td class="center aligned">备注</td>--}}
                                    </tr>
                                    @foreach($deliveries as $delivery)
                                        <tr>
                                            <td class="center aligned">{{$delivery->project->name}}</td>
                                            <td class="center aligned">
                                                {{$delivery->statText()}}
                                            </td>
                                            <td class="center aligned">
                                                {{$delivery->amount}}
                                            </td>
                                            <td>
                                                {{$delivery->updated_at}}
                                            </td>
                                            <td>
                                                {{$delivery->name}}
                                            </td>
                                            <td>
                                                {{$delivery->phone}}
                                            </td>
                                            <td>
                                                {{$delivery->province . $delivery->city . $delivery->area . $delivery->addr}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$assets->links()}}
                        </div>
                    </div>
                    <div class="thirteen wide column article">
                        <h4 class="ui button primary"><a style="color: white;" href="/artbc/flows">赠品列表（当前余额：{{$artbc}}）</a></h4>
                    </div>

                </div>


            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
