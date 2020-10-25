
@extends('layouts.admin')

@section('title', '会员财务明细')

@section('content')
    <div class="page-title">
        <h2>会员财务明细</h2>
    </div>
    <div class="widget widget-default widget-item-icon" style="width: 30%">
        <div class="widget-item-left">
            <span class="fa fa-user"></span>
        </div>
        <div class="widget-data">
            <div class="widget-title">余额:</div>
            <div class="widget-int">¥ <span data-toggle="counter" data-to="1564">{{$balance}}</span></div>
            <div class="widget-subtitle">{{date('d/m/Y',time())}}</div>
        </div>
    </div>

    <div class="widget widget-default widget-item-icon" style="width: 30%; margin-left: 60px">
        <div class="widget-item-left">
            <span class="fa fa-user"></span>
        </div>
        <div class="widget-data">
            <div class="widget-title">资产份数:</div>
            <div class="widget-int"> <span data-toggle="counter" data-to="1564">{{$has}}</span></div>
            <div class="widget-subtitle">{{date('d/m/Y',time())}}</div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>资产</th>
                    <th>财务类型</th>
                    <th>金额</th>
                    <th>数量</th>
                    <th>余额</th>
                    <th>内容</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($finance as $value)
                    <tr>
                        <td>{{$value->assetType->name}}</td>
                        <td>{{$value->financeType->name}}</td>
                        <td>
                            @if($value->balance > 0)
                                +{{$value->balance}}
                            @else
                                {{$value->balance}}
                            @endif
                        </td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->after_amount}}</td>
                        <td>{{$value->content}}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$finance->links()}}
        </div>
    </div>
@endsection