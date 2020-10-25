@extends('layouts.admin')

@section('title', '新增项目管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('project')}}"><i class="fa fa-reply"></i></a> 新增项目</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增项目
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('project/create')}}" id="form" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="txid" value="4b2dbf0a4c4da75e06d79d07d3dccd5c7b084be8" id="txid" />
                    <input type="hidden" name="artbc_price" value="0" />
                    <input type="hidden" name="contractAddress" value="4b2dbf0a4c4da75e06d79d07d3dccd5c7b084be8" id="contractAddress" />
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => old('name'), 'placeholder' => "请输入项目名称", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '符号', 'name' => 'asset_code', 'value' => old('asset_code'), 'placeholder' => "请输入Token符号", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '经纪机构', 'name' => 'agent', 'value' => old('agent'), 'placeholder' => "请输入经纪机构", 'ext'=>'required']) !!}
                    {!! Form::bsFile(['name' => 'picture', 'id' => 'filename2','value'=>old('picture'), 'title' => "上传图片",'ext'=>'required','url'=>[]]) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">产品介绍:</label>
                        <div class="col-md-10">
                            <script id="container" name="desc" type="text/plain"></script>
                        </div>
                    </div>

                    {!! Form::bsNumber(['label' => '兑换规则', 'name' => 'rule', 'value' => old('rule'), 'placeholder' => "兑换比例1个qcash可以兑换多少项目token",'ext'=>'required step=0.01 min=0.01']) !!}
                    {!! Form::bsText(['label' => '兑换规则单位', 'name' => 'rule_desc', 'value' => old('rule_desc'), 'placeholder' => "兑换单位，如幅",'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '单价', 'name' => 'price', 'value' => old('price'), 'placeholder' => "请输入单价",'ext'=>'required step=0.01 min=0.01']) !!}
                    {!! Form::bsText(['label' => '单价单位', 'name' => 'price_unit', 'value' => old('price_unit'), 'placeholder' => "单位，如qcash/Token",'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '总数', 'name' => 'total', 'value' => old('total'), 'placeholder' => "请输入总数", 'ext'=>'required ']) !!}
                    {!! Form::bsNumber(['label' => '认购额度', 'name' => 'limit', 'value' => old('limit'), 'placeholder' => "请输入认购额度", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '单人认购额度', 'name' => 'per_limit', 'value' => old('per_limit'), 'placeholder' => "请输入单用户可认购额度", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '所属年代', 'name' => 'age', 'value' => old('age'), 'placeholder' => "请输入所属年代", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '初始认购数量', 'name' => 'init_sold', 'value' => old('init_sold'), 'placeholder' => "请输入初始化时已认购数量", 'ext'=>'required']) !!}

                    <div class="form-group">
                        <label class="col-md-2 control-label">开始时间</label>
                        <div class="col-md-5">
                            <input id="d4311" required class="wdate form-control" type="text" name="start" value="{{old('start')}}" onFocus='WdatePicker({"maxDate": "2022-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="开始时间">
                            {{--<input type="text" name="start" required class="form-control datepicker" value="{{old('start')}}">--}}
                            </div>
                        </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">结束时间</label>
                        <div class="col-md-5">
                            <input id="d4311" required class="wdate form-control" type="text" name="end" value="{{old('end')}}" onFocus='WdatePicker({"maxDate": "2022-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="结束时间">
                            {{--<input type="text" name="end" required class="form-control datepicker" value="{{old('end')}}">--}}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" checked="checked" value="1" type="radio" class="iradio"/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="is_show" value="0" type="radio" class="iradio"/> 否</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-10">
                            <input type="submit" id="submit" class="btn btn-danger btn-lg" value="提交">
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
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-select.js"></script>
<script src="/js/qweb3.js" type="text/javascript"></script>
<script src="/js/TokenFactoryAbi.js" type="text/javascript"></script>
<script src="/js/TokenFactoryContract.js" type="text/javascript"></script>
<script type="text/javascript">
    $("#form").on("submit", async function() {
        if (qrypto.account.address == '') {
            swal({
                title: "",
                text:" 钱包已掉线，请重连",
                type: "info",
                confirmButtonText: "确定",
            }, function () {
            })
            return;
        }
        const web3 = new qweb3.Qweb3(window.qrypto.rpcProvider);        
        const contract = web3.Contract(contractAddress, TokenFactoryAbi);
        var name = $("input[name=name]").val();
        var symbol = $("input[name=asset_code]").val();
        var rule = parseInt($("input[name=rule]").val()); //兑换比例
    
        const tx = await contract.send('create', {
            methodArgs: [name, symbol, 8, 100000, 100000000, rule],    // Sets the function params
            amount: 0,
            gasLimit: 200000,  // Sets the gas limit to 1 million
            gasPrice: 40,
            senderAddress: qrypto.account.address,
        });
        console.log(tx);
        if (tx == undefined || tx.txid == undefined) {
            swal({
                title: "",
                text:"token 发布失败",
                type: "info",
                confirmButtonText: "确定",
            }, function () {
            })
            return;
        }else{
            $("#txid").val(tx.txid);
            $("#contractAddress").val(tx.txid);
        }
    });
    var ue = UE.getEditor('container');
    ue.ready(function() {
        ue.initialFrameHeight = 1000;
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
    });
</script>
@endpush
