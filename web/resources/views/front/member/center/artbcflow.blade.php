@section('title', '赠品列表 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column article">
                        <div class="articleInfo">
                            <h4 class="ui header">赠品列表</h4>
                            <div class="flex_table">
                                <table class="ui very basic celled">
                                    <tbody>
                                    <tr>
                                        <td class="center aligned">赠品名称</td>
                                        <td class="center aligned">数量</td>
                                        <td class="center aligned">类型</td>
                                        <td class="center aligned">时间</td>
                                    </tr>
                                    @foreach($models as $model)
                                        <tr>
                                            <td class="center aligned">ArTBC</td>
                                            <td class="center aligned">
                                                {{$model->amount}}
                                            </td>
                                            <td class="center aligned">
                                                {{$model->typeLabel}}
                                            </td>
                                            <td>
                                                {{$model->created_at}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$models->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
