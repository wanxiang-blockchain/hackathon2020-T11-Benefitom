
@extends('layouts.admin')

@section('title', '个人业绩表')
    <style>
        .datas{
            width: 100%;
            display: flex;
            flex-direction: row;
            text-align: center;
            padding: 25px;
            font-size: 2rem;
            justify-content: space-around;
        }
        @media screen and (max-width: 500px){
            .datas{
                font-size: 1rem;
            }
        }
        .datas > .column{
            width: 28%;
            border: 1px solid #e5e3e4;
            background-color: #d0e9c6;
            padding: 25px;
        }
        .datas > .column > p:last-child{
            color: red;
        }
        #trees{
            height: 1100px;
        }
    </style>

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">锁仓列表</h3>
        </div>
        <div class="search_main">
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="{{route('admin/btscore/sum')}}" method="get" class="form-horizontal" role="form">
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
    <div class="page-title">
        <h2>财务统计</h2>
    </div>
    <div class="datas">
        <div class="column">
            <p>节子点数</p>
            <p>{{$sonNum}}</p>
        </div>
        <div class="column">
            <p>节子点购买量</p>
            <p>{{$sonBuy}}</p>
        </div>
        <div class="column">
            <p>子节点购买单数</p>
            <p>{{round($sonBuy / 1000, 2)}}</p>
        </div>
    </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        {{--<th>排序</th>--}}
                        <th>手机号</th>
                        <th>锁仓积分</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sonLevel1 as $v)
                        <tr>
                            <td><a href="/admin/btscore/sum?phone={{$v['phone']}}" >{{$v['phone']}}</a> </td>
                            <td>{{$v['amount']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

@endsection
