@extends('layouts.admin')
@section('title', '兑换中心——新增商品')
@section('content')
    <div class="col-md-8">
        <h4>新增锁仓</h4>
        <form class="form-horizontal">
            {!! Form::token() !!}
            {!! Form::bsText(['label' => '手机号 ', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入联系方式"]) !!}
            {!! Form::bsText(['label' => '锁仓总量：(版通量*2)', 'name' => 'amount', 'value' => old('amount'), 'placeholder' => "请输入锁仓总量"]) !!}
            <div class="form-group">
                <label class="col-md-2 control-label"></label>
                <div class="col-md-10">
                    <input type="button" id="submit" class="btn btn-danger btn-lg mb-control" value="提交">
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        $('#submit').on('click', function () {
            var form = $('form').serialize();
            $.post('{{route('admin/btshop/create')}}', form, function (res) {
                if(res.code != 200 ) {
                    swal('', res.data, 'error');
                    return false;
                } else {
                    swal('', res.data, 'success');
                    setTimeout(function () {
                        window.location.href = '/admin/btscore/unlock/logs?nav=12|1';
                    }, 1000);
                }
            });
        });
    </script>
@endpush