@section('title', '资金流水 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo">
                            <h4 class="ui header">我的资产</h4>
                            <form class="ui form stackable secondary flex-wrap menu" method="get">
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:70px;">状态：</label>
                                        <select name="is_lock">
                                            <<option value="">全部</option>
                                            <<option @if(request()->get('is_lock') == 1) selected @endif value="1">冻结</option>
                                            <<option @if(request()->get('is_lock') == 2) selected @endif value="2">正常</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:80px;">资产：</label>
                                        <select name="code">
                                            <<option value="">全部</option>
                                            @foreach($asset_types as $asset_type)
                                            <<option @if(request()->get('code') == $asset_type->code) selected @endif value="{{$asset_type->code}}">{{$asset_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui input">
                                        <input id="d4311" class="wdate" type="text" name="beginTime" value="{{request()->get('beginTime')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="开始时间">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui input">
                                        <input id="#d4312" class="wdate" type="text" name="endTime" value="{{request()->get('endTime')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="结束时间">
                                    </div>
                                </div>
                                <div class="right menu">
                                    <div class="item">
                                        <input type="submit" value="查询" class="ui orange button">
                                    </div>
                                </div>
                            </form>
                            <div class="flex_table">
                                <table class="ui very basic celled">
                                    <tbody>
                                    <tr>
                                        <td class="center aligned">资产名称</td>
                                        <td class="center aligned">总计</td>
                                        <td class="center aligned">状态</td>
                                        <td class="center aligned">参考成本</td>
                                        <td class="center aligned">可卖</td>
                                        <td class="center aligned">提货</td>
                                        <td class="center aligned">解冻时间</td>
                                        {{--<td class="center aligned">描述信息</td>--}}
                                    </tr>
                                    @foreach($assets as $asset)
                                        <tr>
                                            <td class="center aligned">{{$asset->asset_name}}</td>
                                            <td class="center aligned">
                                                {{number_format($asset->amount, 2)}}
                                                @if($asset->asset_name == '现金')
                                                    qcash
                                                @else
                                                    份
                                                @endif
                                            </td>
                                            <td class="center aligned">
                                                @if($asset->is_lock == 1)
                                                    冻结
                                                @else
                                                    正常
                                                @endif
                                            </td>
                                            <td class="center aligned">
                                                {{number_format($asset->cost, 2)}}
                                            </td>
                                            <td class="center aligned">
                                                {{$asset->trade_amount}}
                                            </td>
                                            <td class="center aligned">
                                                @if($asset->trade_amount > 0)
                                                    <a class="positive ui button" href="/member/delivery/{{$asset->id}}">提货</a>
                                                @else
                                                @endif
                                            </td>
                                            <td class="center aligned">
                                                @if($asset->is_lock == 0 ||  $asset->order_id>0)
                                                @else
                                                    {{$asset->unlock_time}}
                                                @endif
                                            </td>
                                            {{--<td class="center aligned">文本标签</td>--}}
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$assets->links()}}
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
