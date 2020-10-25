
@extends('layouts.admin')

@section('title', '锁仓列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">充值列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/block/recharges')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">txHash</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="tx" id="" type="text" value="{{request()->get('tx')}}" placeholder="请输入tx">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">订单号</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="order_num" id="" type="text" value="{{request()->get('order_num')}}" placeholder="请输入tx">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-2 col-lg-2">
                                <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <p><a href="{{url('admin/btscore/unlock/create')}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>MID</th>
                    <th>手机</th>
                    <th>tx</th>
                    <th>订单号</th>
                    <th>stat</th>
                    <th>code</th>
                    <th>数量</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->member->id}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>
                            @if(empty($value->tx))
                                <a data-order_num="{{ $value['order_num'] }}" class="btn btn-default btn-sm append">补充</a>
                            @else
                                @if($value->code === '300001')
                                    <a target="_blank" href="https://etherscan.io/tx/{{$value->tx}}" >{{$value->tx}}</a>
                                @else
                                    {{$value->tx}}
                                @endif
                            @endif
                        </td>
                        <td>{{$value->order_num}}</td>
                        <td>{{\App\Model\Btshop\BlockRechargeLog::statLabel($value->stat)}}</td>
                        <td>{{$value->code}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            {{--<a href="" class="btn btn-default btn-sm">释放记录</a>--}}
                            @if($value->stat === \App\Model\Btshop\BlockRechargeLog::STAT_DONE)
                                <a href="javascript:;" class="del" data-auid="{{$value->id}}" class="btn btn-default btn-sm">扣除</a>
                            @else
                                <a href="javascript:;" class="revise" style="color: red;" data-auid="{{$value->id}}" class="btn btn-warning btn-sm">补发</a>
                            @endif
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal{{$value['order_num']}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">交易id</h4>
                                </div>
                                <div class="modal-body">
                                    {!! $value['tx'] !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                    }
                });
                $('.append').on('click', function () {
                    var id = $(this).data('order_num');
                    swal({
                            title: "请输入补充交易id:",
                            text: "",
                            type: "input",
                            inputType:'text',
                            showCancelButton: true,
                            closeOnConfirm: false,
                            animation: "slide-from-top",
                            confirmButtonText: "确定",
                            cancelButtonText: " 取消",
                            inputPlaceholder: "请输入交易id",
                            showLoaderOnConfirm: true
                        },
                        function(inputValue){
                            if (inputValue === false) return false;

                            if (inputValue === "") {
                                swal.showInputError("您没有输入交易id");
                                return false
                            }
                            $.post('/admin/block/recharge/tx/append', {'tx':inputValue, 'order_num':id }, function(result){
                                if(result.code!=200){
                                    swal.showInputError(result.data);
                                }else{
                                    swal({
                                        title: "",
                                        text:"操作成功",
                                        type: "success",
                                        confirmButtonText: "确定",
                                    })
                                    setTimeout(function(){
                                        location.href = location.href;
                                    }, 1000);
                                }
                            });
                        });
                })
            })
        </script>
    @endpush
    <script>
        $(function () {
            $('.del').on('click', function (e) {
                if (confirm('确定删除？')){
                    var id = e.target.dataset['auid']
                    $.post('/admin/block/recharge/del/' + id, {id: id}, function (res) {
                        if(res.code != 200 ) {
                            swal('', res.data, 'error');
                            return false;
                        } else {
                            swal('', res.data, 'success');
                            setTimeout(function () {
                                window.location.href =  window.location.href
                            }, 1000);
                        }
                    });
                }
            })
            $('.revise').on('click', function (e) {
                if (confirm('确定补发？')){
                    var id = e.target.dataset['auid']
                    $.post('/admin/block/recharge/revise/' + id, {id: id}, function (res) {
                        if(res.code != 200 ) {
                            swal('', res.data, 'error');
                            return false;
                        } else {
                            swal('', res.data, 'success');
                            setTimeout(function () {
                                window.location.href =  window.location.href
                            }, 1000);
                        }
                    });
                }
            })
        })
    </script>
@endsection