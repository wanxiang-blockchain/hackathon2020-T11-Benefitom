@extends('layouts.admin')

@section('title', '新增订单管理')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('projectOrder')}}"><i class="fa fa-reply"></i></a> 新增订单</h2>
    </div>
    {{--<div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>添加时,请注意相关错误提示信息,并对错误进行修改.
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
    </div>--}}
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                新增订单
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{route('projectOrder/create')}}" role="form" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="col-md-2 control-label">项目</label>
                        <div class="col-md-5">
                            <select name="project_id" class="form-control select" id="">
                            @foreach($projects as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    {!! Form::bsNumber(['label' => '电话', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入客户电话", 'ext'=>'required']) !!}
                    {!! Form::bsNumber(['label' => '价格', 'name' => 'price', 'value' => old('price'), 'placeholder' => "请输入订单价格", 'ext'=>'required step=0.01']) !!}
                    {!! Form::bsNumber(['label' => '数量', 'name' => 'quantity', 'value' => old('quantity'), 'placeholder' => "请输入数量", 'ext'=>'required']) !!}
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
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-file-input.js?v=1"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-select.js"></script>
@endpush
