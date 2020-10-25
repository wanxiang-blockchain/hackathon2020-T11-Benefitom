
@extends('layouts.admin')

@section('title', '平台资产流水')

@section('content')
    <div class="page-title">
        <h2>平台资产流水</h2>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>金额</th>
                    <th>余额</th>
                    <th>描述</th>
                    <th>创建时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pay as $value)
                    <tr>
                        <td>{{$value->id}}</td>
                        <td>
                            @if($value->real_money > 0)
                                +{{$value->real_money}}
                            @else
                                {{$value->real_money}}
                            @endif

                        </td>
                        <td>{{$value->total_balance}}</td>
                        <td>{{$value->desc}}</td>
                        <td>{{$value->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$pay->links()}}
        </div>
    </div>
@endsection