
@extends('layouts.admin')

@section('title', '锁仓列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">锁仓列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/btscore/unlock/logs')}}" method="get" class="form-horizontal" role="form">
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
            <p><a href="{{url('admin/btscore/unlock/create')}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>手机</th>
                    <th>锁仓总量</th>
                    <th>已释放量</th>
                    <th>释放状态</th>
                    <th>上一释放日期</th>
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
                        <td>{{$value->unlocked_amount}}</td>
                        <td>{{\App\Model\Artbc\BtScoreUnlock::statLabel($value->stat)}}</td>
                        <td>{{$value->last_unlock_time}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            {{--<a href="" class="btn btn-default btn-sm">释放记录</a>--}}
                            <a href="javascript:;" class="del" data-auid="{{$value->id}}" class="btn btn-default btn-sm">删除</a>
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
            $('.del').on('click', function (e) {
                if (confirm('确定删除？')){
                    var id = e.target.dataset['auid']
                    $.post('/admin/btscore/unlock/del/' + id, {id: id}, function (res) {
                        if(res.code != 200 ) {
                            swal('', res.data, 'error');
                            return false;
                        } else {
                            swal('', res.data, 'success');
                            setTimeout(function () {
                                window.location.href = window.location.href
                            }, 1000);
                        }
                    });
                }
            })
        })
    </script>
@endsection