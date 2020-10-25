@section('title', '我的委托-绍德艺品易货中心')
@include('front.layouts.mhead')
<link rel="stylesheet" href="/layui/css/layui.css">
</head>
<body>
<section>
    <table class="layui-table">
        <thead>
            <td>委托楼盘</td>
            <td>委托方向</td>
            <td>委托价格</td>
            <td>委托数量</td>
        </thead>
        @foreach($models as $model)
            <tr class="cancle" data-id="{{$model->id}}">
                <td>{{$model->assetType->name}}</td>
                <td>{{$model->type == 1 ? '买入' : '卖出'}}</td>
                <td>{{$model->price}}</td>
                <td>{{$model->amount}}</td>
            </tr>
        @endforeach
    </table>
</section>
<script type="text/javascript" src="/js/admin/plugins/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script>
    $(function () {
        $('.cancle').on('click', function () {
            var order_id = $(this).data('id')
            swal({
                title: '警告？',
                text: '确定撤单？',
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function () {
                $.get('/trade/inverted', {order_id:order_id}, function (res) {
                    if(res.code != 200) {
                        swal(res.data);
                        return false;
                    }
                    swal({
                        title: '撤单成功'
                    },function () {
                        location.href = location.href
                    });
                });
            })
        })
    })

</script>
@include('front.layouts.mfoot')