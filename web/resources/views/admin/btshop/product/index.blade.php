
@extends('layouts.admin')

@section('title', '兑换中心商品列表')

@section('content')
    <div class="page-title">
        <h2>兑换中心商品列表</h2>
    </div>
    <div class="search_main">
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="{{route('admin/btshop/products')}}" method="get" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">名称</label>
                        <div class="col-sm-8 col-xs-12">
                            <input class="form-control" name="name" id="" type="text" value="{{request()->get('name')}}" placeholder="请输入搜索的标题">
                        </div>
                        <div class="col-xs-12 col-sm-2 col-lg-2">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <p><a href="{{url('admin/btshop/product/create')}}" class="btn btn-default"><i class="fa fa-plus"></i> 添加</a></p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    {{--<th>排序</th>--}}
                    <th>名称</th>
                    <th>图片</th>
                    <th>支付类型</th>
                    <th>ArTBC价格</th>
                    <th>现金价格</th>
                    <th>ARTTBC价格</th>
                    <th>ARTBCS价格</th>
                    <th>积分</th>
                    <th>限购</th>
                    <th>是否开启</th>
                    <th>创建时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        {{--<td>{{$value['id']}}</td>--}}
                        <td>{{$value['name']}}</td>
                        <td>
                            @if($value['img'])
                                <img style="width: 30px;" src="{{\App\Utils\OssUtil::fetchGetSignUrl($value['img'])}}" alt="">
                            @endif
                        </td>
                        <td>{{\App\Model\Btshop\BtshopProduct::payLabel($value['paytype'])}}</td>
                        <td>{{$value['price']}}</td>
                        <td>{{$value['rmb_price']}}</td>
                        <td>{{$value['bt_price']}}</td>
                        <td>{{$value['artbcs_price']}}</td>
                        <td>{{$value['score']}}</td>
                        <td>{{$value['per_limit']}}</td>
                        <td>
                            @if($value['enable'] == 0)
                                <span data-id="{{$value['id']}}" data-stat="1" class="enable btn label label-default">开启</span>
                            @elseif($value['enable'] == 1)
                                <span data-id="{{$value['id']}}"  data-stat="0" class="enable btn  label label-success">关闭</span>
                            @endif
                        </td>
                        <td>{{$value['created_at']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(function () {
            $('.enable').on('click', function () {
                var _this = $(this)
                var id = _this.data('id')
                var enable = _this.data('stat')
                $.post('/admin/btshop/product/enable', {
                    enable: enable,
                    id: id
                }, function (res) {
                    if(res.code != 200) {
                        alert(res.data)
                    }
                    location.href = location.href
                })
            })
        })
    </script>
@endsection
