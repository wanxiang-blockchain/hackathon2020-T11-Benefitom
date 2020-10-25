
@extends('layouts.admin')

@section('title', '销售列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">销售列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('rong/userProduct')}}" method="get" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">手机</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="phone" id="" type="text" value="{{request()->get('phone')}}" placeholder="请输入搜索的手机">
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
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>用户</th>
                    <th>购买产品</th>
                    <th>预计收益率</th>
                    <th>购买期限</th>
                    <th>购买时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->product->name}}</td>
                        <td>{{$value->product->rate}}</td>
                        <td>{{$value->product->duration}}</td>
                        <td>{{$value['created_at']}}</td>
                        <td>
                            <a href="javascript:;" data-upid="{{$value->id}}" class="btn btn-default btn-sm end-audit">审核</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
    <script>
        $(function () {
            $('.end-audit').on('click', function () {
                var _this = $(this)
                rongPromot("确定审核放款", function () {
                    var id = _this.data('upid')
                    $.post('/admin/rong/endAudit/' + id, {}, function (res) {
                        if(res.code != 200) {
                            return rongWarning(res.data)
                        }
                        return rongSuccess(res.data, function () {
                            location.href = location.href
                        })
                    })
                })
            })
        })
    </script>
@endsection