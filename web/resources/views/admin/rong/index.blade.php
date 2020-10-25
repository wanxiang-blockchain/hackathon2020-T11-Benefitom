
@extends('layouts.admin')

@section('title', '产品列表')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">产品列表</h3>
        </div>
        <div class="panel-body">
            <p><a href="{{url('admin/rong/product/create')}}" class="btn btn-primary btn-sm">新增</a></p>
            <table class="table table-hover">
                <thead>
                <tr class="ttitle">
                    <td>产品</td>
                    <td>年化收益</td>
                    <td>期限</td>
                    <td>价格</td>
                    <td>发行数量</td>
                    <td>已售数量</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value['name']}}</td>
                        <td>{{$value['rate']}}</td>
                        <td>{{$value['duration']}}</td>
                        <td>{{$value['price']}}</td>
                        <td>{{$value['amount']}}</td>
                        <td>{{$value['sold_amount']}}</td>
                        <td>{{$value['enable'] == 1 ? '开启' : '未开启'}}</td>
                        <td>
                            <a href="{{route('rong/product/edit') . '/'. $value['id']}}" class="btn btn-default btn-sm">编辑</a>
                            <a onclick="lv_delete(this)" data-url="{{route('rong/product/delete', ['id'=>$value['id']])}}" class="btn btn-default btn-sm">删除</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
    </script>
@endsection