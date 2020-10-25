@section('title', '我的认购 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo">
                            <h4 class="ui header">我的认购</h4>
                            <form class="ui form stackable secondary flex-wrap menu" method="get">
                                <div class="item">
                                    <div class="ui input">
                                        <label style="line-height:38px;width:70px;">状态：</label>
                                        <select name="status" class="form-control">
                                            <option value="" selected="">全部</option>
                                            <option @if(request()->get('status') == 4) selected @endif value="4">未支付</option>
                                            <option @if(request()->get('status') == 1) selected @endif value="1">已支付</option>
                                            <option @if(request()->get('status') == 2) selected @endif value="2">已完成</option>
                                            <option @if(request()->get('status') == 3) selected @endif value="3">已关闭</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item">
                                    <div style="width: 120px">认购日期:</div>
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
                            <div class="flex_table">
                                <table class="ui very basic celled">
                                    <tbody>
                                    <tr>
                                        {{-- <td class="center aligned">项目编号</td>--}}
                                        <td class="center aligned">项目名称</td>
                                        <td class="center aligned">认购时间</td>
                                        <td class="center aligned">认购价格</td>
                                        <td class="center aligned">认购份数</td>
                                        <td class="center aligned">状态</td>
                                    </tr>
                                    @foreach($project as $value)
                                        <tr>
                                            {{-- <td class="center aligned">{{$value->order_id}}</td>--}}
                                            <td class="center aligned">{{$value->project_name}}</td>
                                            <td class="center aligned">{{$value->created_at}}</td>
                                            <td class="center aligned">￥{{$value->price}}qcash</td>
                                            <td class="center aligned">{{$value->quantity}}</td>
                                            <td class="center aligned">
                                                @if($value['status'] == '')
                                                    未支付
                                                @elseif($value['status'] == 1)
                                                    已支付
                                                @elseif($value['status'] == 2)
                                                    已完成
                                                @elseif($value['status'] == 3)
                                                    已关闭
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$project->links()}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
