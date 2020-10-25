@section('title', '交易中心')
@include("front.layouts.head")
<div class="pusher tradeList">
    <div class="ui container">
        <div class="ui breadcrumb">
          <div class="active section">交易中心</div>
        </div>
        <div class="tradeListBox">
            <table class="ui very basic table">
                <thead>
                <tr>
                    <th class="center aligned">资产名称</th>
                    <th class="center aligned">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($asset_types as $asset_type)
                <tr>
                    <td class="center aligned">【{{$asset_type->name}}】</td>
                    <td class="center aligned">
                        <a class="ui inverted brown button" href="{{route('trade/detail',['id' => $asset_type->id])}}">去交易</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@include("front.layouts.foot")
