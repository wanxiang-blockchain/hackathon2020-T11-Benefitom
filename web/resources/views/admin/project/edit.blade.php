@extends('layouts.admin')

@section('title', '新增项目管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('project')}}"><i class="fa fa-reply"></i></a>编辑项目</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>编辑时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                编辑项目
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('project/postEdit')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$project['id']}}"/>
                    <div class="form-group">
                        <label class="col-md-2 control-label">资产类型</label>
                        <div class="col-md-5">
                            <select name="asset_code" class="form-control select" id="">
                                @foreach($asset as $item)
                                    <option @if($item['code'] == $project['asset_code']) selected @endif value="{{$item['code']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => $project['name'], 'placeholder' => "请输入项目名称",'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '楼盘代码', 'name' => 'asset_code', 'value' => $project['asset_code'], 'placeholder' => "请输入楼盘代码", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '经纪机构', 'name' => 'agent', 'value' => $project['agent'], 'placeholder' => "请输入经纪机构", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['name' => 'picture', 'id' => 'filename2','value'=>$project['picture'], 'title' => "上传图片",'url'=>[$project['picture']]]) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">产品介绍:</label>
                        <div class="col-md-10">
                            <script id="container" name="desc" type="text/plain">{!! $project['desc'] !!}</script>
                        </div>
                    </div>

                    {!! Form::bsNumber(['label' => '兑换规则', 'name' => 'rule', 'value' => $project['rule'], 'placeholder' => "请输入兑换规则",'ext'=>'required step=0.01 min=0.01']) !!}
                    {!! Form::bsText(['label' => '兑换规则单位', 'name' => 'rule_desc', 'value' => $project['rule_desc'], 'placeholder' => "兑换单位，如幅",'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '单价', 'name' => 'price', 'value' => $project['price'], 'placeholder' => "请输入单价",'ext'=>'required step=0.01 min=0.01']) !!}
                    {!! Form::bsText(['label' => '单价单位', 'name' => 'price_unit', 'value' => $project['price_unit'], 'placeholder' => "单位，如qcash/套",'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '总数', 'name' => 'total', 'value' => $project['total'], 'placeholder' => "请输入总数",'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '认购额度', 'name' => 'limit', 'value' => $project['limit'], 'placeholder' => "请输入认购额度", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '单人认购额度', 'name' => 'per_limit', 'value' => $project['per_limit'], 'placeholder' => "请输入单用户可认购额度", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '所属年代', 'name' => 'age', 'value' => $project['age'], 'placeholder' => "请输入所属年代", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '认购赠artbc', 'name' => 'artbc_prize', 'value' => $project['artbc_prize'], 'placeholder' => "请输入每股认购赠送artbc数", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '初始认购数量', 'name' => 'init_sold', 'value' => $project['init_sold'], 'placeholder' => "请输入初始化时已认购数量", 'ext'=>'required']) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">开始时间</label>
                        <div class="col-md-5">
                            {{--<input type="text" name="start" class="form-control datepicker" required value="{{$project['start']}}">--}}
                            <input id="d4311" required class="wdate form-control" type="text" name="start" required value="{{$project['start']}}" onFocus='WdatePicker({"maxDate": "2022-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="开始时间">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">结束时间</label>
                        <div class="col-md-5">
                            {{--<input type="text" name="end" class="form-control datepicker" required value="{{$project['end']}}">--}}
                            <input id="d4311" required class="wdate form-control" type="text" name="end" required value="{{$project['end']}}" onFocus='WdatePicker({"maxDate": "2022-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="结束时间">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="1" type="radio" class="iradio" @if($project['is_show']==1) checked="checked"@endif/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="0" type="radio" class="iradio" @if($project['is_show']==0) checked="checked"@endif/> 否</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-10">
                            <input type="submit" class="btn btn-danger" value="提交">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.initialFrameHeight = 1000;
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });
</script>
@endpush
