@extends('layouts.admin')
@section('title', '配置')
@section('content')
    <div class="col-md-8">
        <h4>销售配置</h4>
        <form class="form-horizontal">
            {!! Form::token() !!}
            {!! Form::bsText(['label' => '每单限额', 'name' => 'per_order_amount', 'value' => $model->per_order_amount, 'placeholder' => "每单限额"]) !!}
            {!! Form::bsText(['label' => '每人每天限单数', 'name' => 'per_order_nums', 'value' => $model->per_order_nums, 'placeholder' => "每人每天限单数"]) !!}
            {!! Form::bsText(['label' => '系统每天总限单数', 'name' => 'total_order_nums', 'value' => $model->total_order_nums, 'placeholder' => "系统每天总限单数"]) !!}
            {!! Form::bsText(['label' => '释放周期', 'name' => 'period', 'value' => $model->period, 'placeholder' => "释放周期"]) !!}
            {!! Form::bsText(['label' => '释放百分比', 'name' => 'percent', 'value' => $model->percent, 'placeholder' => "释放百分比"]) !!}
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
        $.post('{{route('admin/btconfig/edit')}}', form, function (res) {
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                return false;
            } else {
                swal('', res.data, 'success');
                setTimeout(function () {
                    window.location.href = window.location.href;
                }, 1000);
            }
        });
    });
</script>
@endpush