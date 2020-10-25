@section('title', '我的交易记录 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column">
                        <div class="articleInfo">
                            <h4 class="ui header">我的成交</h4>
                            <form class="ui form stackable secondary flex-wrap menu" method="get">
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:70px;">状态：</label>
                                        <select class="ui dropdown" name="status">
                                            <<option value="0">全部</option>
                                            <<option value="1" @if(request()->get('status') == 1) selected @endif>买</option>
                                            <<option value="2" @if(request()->get('status') == 2) selected @endif>卖</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item">
                                    <div style="width: 120px">成交日期:</div>
                                    <div class="ui input">
                                        <input id="d4311" class="wdate" type="text" name="beginTime" value="{{request()->get('beginTime')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="开始时间">
                                    </div>
                                </div>
                                <span style="line-height: 53px;">至</span>
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
                                <table class="ui very basic celled ">
                                    <tbody>
                                    <tr>
                                        <td class="center aligned">资产名称</td>
                                        <td class="center aligned">成交时间</td>
                                        <td class="center aligned">成交价格</td>
                                        <td class="center aligned">成交份数</td>
                                        <td class="center aligned">状态</td>
                                    </tr>
                                    @if($trade)
                                        @foreach($trade as $value)
                                            <tr>
                                                <td class="center aligned">{{$value->asset_name}}</td>
                                                <td class="center aligned">{{$value->created_at}}</td>
                                                <td class="center aligned">￥{{$value->price}}qcash</td>
                                                <td class="center aligned">{{$value->amount}}</td>
                                                <td class="center aligned">
                                                    @if($value->buyer_id == $value->seller_id)
                                                        买/卖
                                                    @else
                                                        @if($value->buyer_id == request()->user('front')->id)
                                                            买
                                                        @else
                                                            卖
                                                        @endif
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            {{$trade->links()}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
