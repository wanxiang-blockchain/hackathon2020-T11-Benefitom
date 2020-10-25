@extends('layouts.admin')

@section('title', '新增兑换中心商品')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('admin/btshop/product/create')}}"><i class="fa fa-reply"></i></a> 新增兑换中心商品</h2>
    </div>
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增合作伙伴
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('admin/btshop/product/create')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    {!! Form::bsText(['label' => '名称', 'name' => 'name', 'value' => old('name'), 'placeholder' => "请输入名称", 'ext'=>'required']) !!}
{{--                    {!! Form::bsFile(['name' => 'img', 'id' => 'img','value'=>old('img'), 'title' => "上传图片",'url'=>[]]) !!}--}}
                    <input type="file"
                           id="imgid" name="image"
                           accept="image/png, image/jpeg">
                    <img id="show" src="" />
                    <input id="imgvalue" name="img" type="hidden" value=""/>
                    <div class="form-group">
                        <label class="col-md-2 control-label">支付类型</label>
                        <div class="col-md-10">
                            <select name="paytype" class="form-control">
                                @foreach(\App\Model\Btshop\BtshopProduct::paytypes() as $type => $label)
                                    <option value="{{$type}}">{{$label}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {!! Form::bsText(['label' => 'ArTBC价格', 'name' => 'price', 'value' => old('price'), 'placeholder' => "请输入ArTBC价格"]) !!}
                    {!! Form::bsText(['label' => 'RMB价格', 'name' => 'rmb_price', 'value' => old('rmb_price'), 'placeholder' => "请输入人民币价格"]) !!}
                    {!! Form::bsText(['label' => 'ARTTBC价格', 'name' => 'bt_price', 'value' => old('bt_price'), 'placeholder' => "请输入ARTTBC价格"]) !!}
                    {!! Form::bsText(['label' => 'ARTBCS价格', 'name' => 'artbcs_price', 'value' => old('artbcs_price'), 'placeholder' => "请输入ARTBCS价格"]) !!}

                    {!! Form::bsText(['label' => '积分', 'name' => 'score', 'value' => old('score'), 'placeholder' => "请输入奖励积分数量", 'ext'=>'required']) !!}
                    {!! Form::bsText(['label' => '每天限购', 'name' => 'per_limit', 'value' => old('per_limit'), 'placeholder' => "请输入每天每账户限购数量", 'ext'=>'required']) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">是否开启</label>
                        <div class="col-md-10">
                            <div class="col-md-1">
                                <label class="check"><input name="enable" value="1" type="radio" class="iradio"/> 是</label>
                            </div>
                            <div class="col-md-1">
                                <label class="check"><input name="enable" value="0" type="radio" class="iradio" checked="checked"/> 否</label>
                            </div>
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
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.bootcss.com/lodash.js/4.17.11/lodash.core.js"></script>
<script>
    $(function(){
        $('#imgid').on('change', function (event) {
            console.log(event)
            // 上传图片
            $.post('/admin/osstk', {
                type: 2
            }, function (res) {
                var data = res.data
                var file = event.target.files[0]
                if (data) {
                    data =  Object.assign(data, {file: file})
                    console.log(data)
                    var xhr = new XMLHttpRequest()
                    var formData = new FormData()
                    formData.append('name', file.name)
                    formData.append('success_action_status', 200)
                    // eslint-disable-next-line
                    // console.log(file)
                    var now = Date.parse(new Date()) / 1000
                    var type = file.name.split('.').pop()
                    var filename = now + '.' + type
                    formData.append('key', data.dir + filename)
                    formData.append('policy', data.policy)
                    formData.append('OSSAccessKeyId', data.accessid)
                    formData.append('signature', data.signature)
                    formData.append('file', file, file.name)
                    xhr.open('post', data.host, true)
                    xhr.send(formData)
                    $('#imgvalue').val(data.dir + filename)
                    $('#img').val(data.dir + filename)
                }
            })
        })
    })

</script>
@endpush
