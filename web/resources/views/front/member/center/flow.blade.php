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
                            <h4 class="ui header">资金流水</h4>
                            <form class="ui form stackable secondary flex-wrap menu" method="get">
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:70px;">状态：</label>
                                        <select  class="form-control" name="status">
                                            <<option value="">全部</option>
                                            <<option value="1" @if(request()->get('status') == 1) selected @endif>收入</option>
                                            <<option value="2" @if(request()->get('status') == 2) selected @endif>支出</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="item">
                                    <div style="width: 120px">发生日期:</div>
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
                                <div class=" menu">
                                    <div class="item">
                                        <input type="submit" value="查询" class="ui orange button">
                                    </div>
                                </div>
                            </form>
                            <div class="flex_table" >
                                <table class="ui very basic celled">
                                    <tbody>
                                    <tr>
                                        <td class="center aligned">发生日期</td>
                                        <td class="center aligned">操作</td>
                                        <td class="center aligned">资产类型</td>
                                        <td class="center aligned">收/支金额</td>
                                        <td class="center aligned">余额</td>
                                        <td class="center aligned">备注</td>
                                    </tr>
                                    @foreach($flow as $value)
                                        <tr>
                                            <td class="center aligned">{{$value->created_at}}</td>
                                            <td class="center aligned">
                                                {{$value->type_name}}
                                            </td>
                                            <td class="center aligned">{{$value->asset_name}}</td>
                                            <td class="center aligned">{{str_replace('-', '支', number_format($value->balance, 2))}}</td>
                                            <td class="center aligned">{{number_format($value->after_amount, 2)}}</td>
                                            <td class="center aligned">{{$value->content}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$flow->links()}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
