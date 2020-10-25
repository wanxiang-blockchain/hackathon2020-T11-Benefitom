@extends('layouts.admin')
@section('title', '新增代理 ')
@section('content')
    <div class="col-md-8">
        <h4>新增会员</h4>
        <form class="form-horizontal" action="{{route('agent/create')}}" role="form" method="post" enctype="multipart/form-data">
            {!! Form::token() !!}
            {!! Form::bsText(['label' => '手机号 ', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入联系方式"]) !!}
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
        $.post('{{route('agent/create')}}', form, function (res) {
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                return false;
            } else {
                swal('', res.data, 'success');
                setTimeout(function () {
                    window.location.href = '/admin/agent?nav=4|7';
                }, 1000);
            }
        });
    });
</script>
@endpush