
@extends('layouts.admin')

@section('title', '交易设置')

@section('content')
    <div class="page-title">
        <h2>交易设置</h2>
    </div>
    <div class="panel panel-danger">
        <div class="panel-body">
            <div class="panel panel-default tabs">
                <ul class="nav nav-tabs" role="tablist">
                    @foreach($assetType as $k=>$item)
                    <li @if(!$k) class="active" @endif><a href="#{{$item['code']}}" role="tab" data-toggle="tab">{{$item['name']}}</a></li>
                    @endforeach
                </ul>
                <div class="panel-body tab-content">
                    @foreach($assetType as $k=>$item)
                        <div class="tab-pane @if(!$k) active @endif" id="{{$item['code']}}">
                            <div class="block">
                                <form class="form-horizontal" role="form" method="post" action="{{route('trade/setPost')}}">
                                    {{csrf_field()}}
                                    <input type="hidden" name="asset_type" value="{{$item['code']}}">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">上午开盘时间</label>
                                        <div class="col-md-5">
                                            <input id="d4311" required class="wdate form-control" type="text" name="start" value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['start']}}@endif" onFocus='WdatePicker({"dateFmt": "HH:mm:ss"})' placeholder="开始时间">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">上午收盘时间</label>
                                        <div class="col-md-5">
                                            <input id="d4311" required class="wdate form-control" type="text" name="end" value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['end']}}@endif" onFocus='WdatePicker({ "dateFmt": "HH:mm:ss"})' placeholder="结束时间">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">下午开盘时间</label>
                                        <div class="col-md-5">
                                            <input id="d4311" required class="wdate form-control" type="text" name="start2" value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['start2']}}@endif" onFocus='WdatePicker({"dateFmt": "HH:mm:ss"})' placeholder="开始时间">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">下午收盘时间</label>
                                        <div class="col-md-5">
                                            <input id="d4311" required class="wdate form-control" type="text" name="end2" value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['end2']}}@endif" onFocus='WdatePicker({ "dateFmt": "HH:mm:ss"})' placeholder="结束时间">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">资产交易中心开始时间</label>
                                        <div class="col-md-5">
                                            <input id="d4311" required class="wdate form-control" type="text" name="trade_start" value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['trade_start']}}@endif" onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd"})' placeholder="交易中心开始时间">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">手续费</label>
                                        <div class="col-md-5">
                                            <input type="number" name="rate" class="form-control" required step=0.001 min=0.001 value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['rate']}}@endif" placeholder="请输入手续费"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">跌停板限制</label>
                                        <div class="col-md-5">
                                            <input type="number" name="limit" class="form-control" required step=0.01 min=0.01 value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['limit']}}@endif" placeholder="请输入限制数"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">T+</label>
                                        <div class="col-md-5">
                                            <input type="number" name="t_plus" class="form-control" required step=1 min=0 value="@if(isset($tradeSet[$item['code']])){{$tradeSet[$item['code']]['t_plus']}}@endif" placeholder="请输入限制数"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label"></label>
                                        <div class="col-md-10">
                                            <input type="submit" class="btn btn-danger btn-lg" value="提交">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-select.js"></script>
@endpush