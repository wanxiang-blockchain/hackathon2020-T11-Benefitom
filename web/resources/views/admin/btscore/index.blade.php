
@extends('layouts.admin')

@section('title', '积分流水')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">锁仓列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/btscore/logs')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">变更类型</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="type" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option @if(request()->get('type') == 1) selected @endif value="1">锁仓释放</option>
                                    <option @if(request()->get('type') == 2) selected @endif value="2">提取</option>
                                    <option @if(request()->get('type') == 3) selected @endif value="3">提取驳回</option>
                                    <option @if(request()->get('type') == 4) selected @endif value="4">一级推荐奖励</option>
                                    <option @if(request()->get('type') == 5) selected @endif value="5">二级推荐奖励</option>
                                    <option @if(request()->get('type') == 6) selected @endif value="6">系统修复</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">变更类型</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name="stat" class="form-control">
                                    <option value="" selected="">全部</option>
                                    <option @if(request()->get('stat') == 0) selected @endif value="0">提取待审核</option>
                                    <option @if(request()->get('stat') == 1) selected @endif value="1">审核通过</option>
                                    <option @if(request()->get('stat') == 2) selected @endif value="2">提取驳回</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">时间</label>
                            <div class="col-xs-12 col-sm-8 col-lg-2 ">
                                <input type="text" class="form-control datepicker" name="beginTime" value="{{request()->get('beginTime')}}" placeholder="开始时间">
                            </div>
                            <div class="col-xs-12 col-sm-8 col-lg-2 ">
                                <input type="text" class="form-control datepicker" name="endTime" value="{{request()->get('endTime')}}" placeholder="结束时间">
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
                <a href="{{url('admin/excel/btscoreExport?=phone'.request()->get('phone').'&type='.request()->get('type').'&beginTime='.request()->get('beginTime').'&endTime='.request()->get('endTime'))}}" class="btn btn-default"><i class="fa fa-cloud-download"></i>提现导出</a>
                {{--<a  class="btn btn-default"> 本页总计:{{$page_sum}}</a>--}}
                {{--<a  class="btn btn-default"> 总计:{{$f_sum}}</a>--}}
                </p>
            </div>

        </div>
        <div class="panel-body">
            <p><a href="{{url('admin/btscore/unlock/create')}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>手机</th>
                    <th>数量</th>
                    <th>实际拨款</th>
                    <th>手续费</th>
                    <th>购物积分</th>
                    <th>变更类型</th>
                    <th>余额</th>
                    <th>版通账户</th>
                    <th>收款账户</th>
                    <th>收款账户名</th>
                    <th>收款开户行</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->type == 2 ? round($value->amount * 0.8, 2) : 0}}</td>
                        <td>{{$value->fee}}</td>
                        <td>{{$value->shopping_score}}</td>
                        <td>{{$value->typeLabel}}</td>
                        <td>{{$value->balance}}</td>
                        <td>{{$value->btaccount}}</td>
                        <td>{{$value->card}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value->bank}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            @if($value->type == \App\Model\Artbc\BtScoreLog::TYPE_TIBI)
                                @if($value->stat == \App\Model\Artbc\BtScoreLog::STAT_INIT)
                                    <a href="javascript:;" class="audit" data-auid="{{$value->id}}" class="btn btn-info btn-sm">审核</a>
                                    {{--<a href="javascript:;" class="reject" data-auid="{{$value->id}}" class="btn btn-default rejecrt">驳回</a>--}}
                                @elseif($value->stat == \App\Model\Artbc\BtScoreLog::STAT_DONE)
                                    审核通过
                                @elseif($value->stat == \App\Model\Artbc\BtScoreLog::STAT_REJECT)
                                    已驳回：{{$value->note}}
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
    <script>
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                }
            });
            $('.audit').on('click', function () {
                var id = $(this).data('auid');
                swal({
                        title: "确定审核通过吗?",
                        text: "通过后对应的资产将提现到用户版通账户",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "是",
                        cancelButtonText: "否",
                        closeOnConfirm: false,
                    },
                    function(){
                        $.ajax({
                            url: '/admin/btscore/audit',
                            data: {id: id, stat: 1},
                            type:'post',
                            dataType:'json',
                            success: function (res) {
                                if(res.code != 200) {
                                    show_message('设置失败', res.data,'error');
                                } else {
                                    show_message('设置成功', '你已经设置成功', 'success');
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 500);
                                }
                            },
                            error: function () {
                                show_message('设置失败', '请检查您的网络参数', 'error');
                            }
                        });

                    });
             })
            $('.reject').on('click', function () {
                var id = $(this).data('auid');
                swal({
                        title: "请输入驳回原因:",
                        text: "",
                        type: "input",
                        inputType:'text',
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        confirmButtonText: "确定",
                        cancelButtonText: " 取消",
                        inputPlaceholder: "请输入驳回原因"
                    },
                    function(inputValue){
                        if (inputValue === false) return false;

                        if (inputValue === "") {
                            swal.showInputError("您没有输入驳回原因");
                            return false
                        }
                        $.post('/admin/btscore/audit', {'note':inputValue, 'id':id, stat: 2 }, function(result){
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
                                    window.location.reload();
                                }, 1000);
                            }
                        });
                    });
            })
        })
    </script>
@endsection