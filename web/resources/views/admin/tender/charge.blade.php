@extends('layouts.admin')

@section('title', '管理员分配小红花')
@section('content')
    <div class="page-title">
        <h2><a href="{{route('finance/recharge')}}"><i class="fa fa-reply"></i></a> 管理员分配小红花</h2>
    </div>
    <div class="col-md-12">
        @include('layouts.error')
        <div class="panel panel-info">
            <div class="panel-heading">
                管理员分配小红花
            </div>
            <div class="panel-body">
                <form class="form-horizontal" id="form" action="{{route('tender/charge')}}" role="form" method="post">
                    {{csrf_field()}}
                    <textarea id="key" style="display: none">{!! $key !!}</textarea>
                    {!! Form::bsText(['label' => '用户手机号', 'name' => 'phone', 'value' => old('phone'), 'placeholder' => "请输入用户手机号", 'ext'=>'required']) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">充值数量</label>
                        <div class="col-md-10">
                            <input type="text" name="amount" min="1" step="0.01" class="form-control money"  required   placeholder="请输入充值金额" value="{{old('amount')}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">充值类型</label>
                        <div class="col-md-10">
                            <select name="type">
                                <option value="">选择类型</option>
                                <option @if(old('type') == '0') selected @endif value="0">充值</option>
                                <option @if(old('type') == '1') selected @endif value="1">赠送</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">管理员密码</label>
                        <div class="col-md-10">
                            <input style="position: absolute; top: -1000px;" type="password" name="password1" value=""/>
                            <input type="password" name="password" id="password" class="form-control"  value="{{old('password')}}"  placeholder="请输入密码"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-10">
                            <input type="button" id="submit" class="btn btn-danger btn-lg mb-control" value="提交">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="message-box message-box-warning animated fadeIn" id="message-box-warning">
        <div class="mb-container">
            <div class="mb-middle">
                <div class="mb-title"><span class="fa fa-warning"></span> 确认充值?</div>
                <div class="mb-content">
                    <h3 style="color: #fff">
                        <p>充值用户: <strong>12121212121</strong></p>
                        <p>充值数量: <strong>12121212121</strong></p>
                    </h3>
                </div>
                <div class="mb-footer">
                    <button class="btn btn-danger btn-lg pull-right mb-control-close" id="form_submit">确定</button>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 8px">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>用户</th>
                    <th>数量</th>
                    <th>充值时间</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($models as $value)
                    <tr>
                        <td>{{$value->member->phone}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>{{$value->note}}</td>
                        <td>
                            @if($value['stat'] == 0)
                                <a data-id="{{$value['id']}}" class="btn btn-info btn-sm accept">通过</a>
                                <a data-id="{{ $value['id'] }}" class="btn btn-default btn-sm reject">驳回</a>
                            @elseif($value['stat'] == 2)
                                <a  class="btn btn-info btn-sm" >已驳回</a>

                            @elseif($value['stat'] == 1)
                                <a class="btn btn-info  btn-sm">已完成</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$models->links()}}
        </div>
    </div>
@endsection
@push('scripts')
<script>
    $('#submit').on('click',function(){
        $('.btn').attr('disabled', true);
    });
    $('#form_submit').on('click', function () {
        $('.btn').attr('disabled',true);
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var old = $('#password').val();
        var enc = crypt.encrypt(old);
        $('#password').val(enc);
        var form = $('form').serialize();
        $('#password').val(old);
        $.post('/admin/tender/charge', form, function (res) {
            console.log(res);
            if(res.code != 200 ) {
                swal('', res.data, 'error');
                $('.btn').attr('disabled',false);
                return false;
            } else {
                swal('', res.data, 'success');
                location.href='/admin/tender/charge';
            }
        });
    });
    $('#submit').on('click', function (e) {
        e.stopPropagation();
        var amount = $('input[name=amount]').val();

        if(!/^[0-9]*[1-9][0-9]*$/.test(amount)) {
            $('.btn').attr('disabled', false);
            swal('', '请输入正确的整数', 'error');
            return false;
        }

        $('.btn').attr('disabled', false);
        $("#message-box-warning").toggleClass("open");
    });

    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : "{{ csrf_token() }}"
            }
        });
        $('.reject').on('click', function () {
            var id = $(this).data('id');
            swal({
                    title: "请输入驳回原因:",
                    text: "",
                    type: "input",
                    inputType:'text',
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    confirmButtonText: "确定",
                    cancelButtonText: " 取消",
                    inputPlaceholder: "请输入驳回原因"
                },
                function(inputValue){
                    if (inputValue === false) return false;

                    if (inputValue === "") {
                        swal.showInputError("您没有输入驳回原因");
                        return false
                    }
                    $.post('/admin/tender/chargeReject', {'note':inputValue,'id':id }, function(result){
                        if(result.code!=200){
                            swal.showInputError(result.message);
                        }else{
                            swal({
                                title: "",
                                text:"操作成功",
                                type: "success",
                                confirmButtonText: "确定",
                            })
                            setTimeout(function(){
                                location.href = '/admin/tender/charge';
                            }, 1000);
                        }
                    });
                });
        })
    })
    $('.accept').on('click', function () {
        var id = $(this).data('id')
        swal({
                title: "确定审核通过吗?",
                text: "通过后对应的资产将充值到用户账户",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "是",
                cancelButtonText: "否",
                closeOnConfirm: false,
            },
            function(){
                $.ajax({
                    url:'/admin/tender/chargeAccept/' + id,
                    type:'post',
                    dataType:'json',
                    success: function (res) {
                        if(res.code != 200) {
                            show_message('设置失败', res.data,'error');
                        } else {
                            show_message('设置成功', '你已经设置成功', 'success');
                            location.href = location.href
                        }
                    },
                    error: function () {
                        show_message('设置失败', '请检查您的网络参数', 'error');
                    }
                });

            });
    })


</script>
@endpush
