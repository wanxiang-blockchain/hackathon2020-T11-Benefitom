@section('title', '委托管理 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo">
                            <h4 class="ui header">委托管理</h4>
                            <form class="ui form stackable secondary flex-wrap menu" method="get">
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:70px;">状态：</label>
                                        <select name="status">
                                            <option value="" selected="">全部</option>
                                            <option @if(request()->get('status') == 4) selected @endif value="4">挂单</option>
                                            <option @if(request()->get('status') == 1) selected @endif value="1">部分成交</option>
                                            <option @if(request()->get('status') == 2) selected @endif value="2">成交</option>
                                            <option @if(request()->get('status') == 3) selected @endif value="3">撤销</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:70px;">类型</label>
                                        <select name="type">
                                            <option value="" selected="">全部</option>
                                            <option @if(request()->get('type') == 1) selected @endif value="1">买入</option>
                                            <option @if(request()->get('type') == 2) selected @endif value="2">卖出</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui input">
                                        {{-- <div style="width: 120px">委托时间:</div>--}}
                                        <input id="d4311" class="wdate" type="text" name="beginTime" value="{{request()->get('beginTime')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="委托开始时间">
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="ui input">
                                        <input id="#d4312" class="wdate" type="text" name="endTime" value="{{request()->get('endTime')}}" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="委托结束时间">
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
                                        <td class="center aligned">委托时间</td>
                                        <td class="center aligned">资产名称</td>
                                        <td class="center aligned">委托价格</td>
                                        <td class="center aligned">类型</td>
                                        <td class="center aligned">成交/委托</td>
                                        <td class="center aligned">状态</td>
                                    </tr>
                                    @if($trade)
                                        @foreach($trade as $value)
                                            <tr>
                                                <td class="center aligned">{{$value->created_at}}</td>
                                                <td class="center aligned">{{$value->asset_name}}</td>
                                                <td class="center aligned">{{$value->price}}qcash</td>
                                                <td class="center aligned">
                                                    @if($value->type == 1)
                                                        买
                                                    @else
                                                        卖
                                                    @endif
                                                </td>
                                                <td class="center aligned">{{($value->quantity - $value->amount) . '/' . $value->quantity}}</td>
                                                <td class="center aligned">
                                                    @if($value->status == 0)
                                                        挂单
                                                    @elseif( $value->status == 1)
                                                        部分成交
                                                    @elseif($value->status == 2)
                                                        成交
                                                    @else
                                                        已撤销
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            {{$trade->appends(request()->all())->links()}}
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
