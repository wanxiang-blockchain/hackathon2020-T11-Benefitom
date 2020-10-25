@extends('layouts.admin')
@section('title', '新增锁仓')
@section('content')
    <div class="col-md-8">
        <h4>新增锁仓</h4>
        <form class="form-horizontal">
            {!! Form::token() !!}
            {!! Form::bsText(['label' => '手机号 ', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入联系方式"]) !!}
            {!! Form::bsText(['label' => '锁仓总量', 'name' => 'amount', 'value' => old('amount'), 'placeholder' => "请输入锁仓总量"]) !!}
            {!! Form::bsText(['label' => '释放次数', 'name' => 'unlock_times', 'value' => old('unlock_times'), 'placeholder' => "请输入释放次数"]) !!}
            {!! Form::bsText(['label' => '释放周期（天）', 'name' => 'unlock_period', 'value' => old('unlock_period'), 'placeholder' => "请输入释放周期"]) !!}
            {!! Form::bsText(['label' => '开始释放日期', 'name' => 'start_unlock_day', 'value' => old('start_unlock_day'), 'placeholder' => "请输入开始释放日期"]) !!}
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
        $.post('{{route('admin/artbc/unlock/edit')}}', form, function (res) {
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                return false;
            } else {
                swal('', res.data, 'success');
                setTimeout(function () {
                    window.location.href = '/admin/artbc/unlocks?nav=11|3';
                }, 1000);
            }
        });
    });
</script>
@endpush