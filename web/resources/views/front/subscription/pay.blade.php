@section('title', '益通云认购中心')
@include('front.layouts.head')
<script type="text/javascript" src="../js/jsencrypt.min.js"></script>
<div class="order pusher">
  <div class="payMain">
    <div class="ui container">
        <div class="ui items">
            <textarea id="key" style="display: none">{!! $key !!}</textarea>
            <div class="item">
                <div class="image">
                    <img src="{{asset('storage/'. $picture)}}">
                </div>
                <div class="content">
                    <a class="header">{{$name}}</a>
                    <p class="order_copies">
                        <img src="/front/image/project_icon2.png">
                        认购楼盘数：{{$amount}}份
                    </p>
                    <div class="ui inverted section divider"></div>
                    <p>总价：￥{{$total_amount}}qcash</p>
                    <div class="ui equal width stackable internally grid">
                        <div class="aligned row">
                            <div class="column">
                                余额：￥{{$balance}}qcash
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ts-payment">
            <div class="ui inverted section divider"></div>
            <div class="order_users">
                <span>项目名称：{{$name}}</span>
                <span>余额：￥{{$balance}}qcash</span>
                @if ($need > 0)
                <a href="/member/recharge" class="ts-order-Btn" id="recharge">去充值</a>
                @else
                <a href="javascript:void 0;" class="ts-order-Btn" id="pay">确认认购</a>
                @endif
            </div>
        </div>
    </div>
  </div>
</div>

@include('front.layouts.foot')
<script type="text/javascript">
    var is_trade_pwd = '{{$is_trade_pwd}}';
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : "{{ csrf_token() }}"
            }
        });
        function pwdDialog(){
            if(is_trade_pwd != 1) {
                swal({
                    title: "",
                    text:" 您还未设置交易密码,确定前往设置吗?",
                    type: "info",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    confirmButtonText: "确定",
                    cancelButtonText: " 取消",
                }, function () {
                    location.href = '/member/resetTradePassword';
                })
                return false;
            }
            swal({
                    title: "请输入交易密码:",
                    text: "",
                    type: "input",
                    inputType:'password',
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    confirmButtonText: "确定",
                    cancelButtonText: " 取消",
                    inputPlaceholder: "请输入交易密码"
                },
                function(inputValue){
                    if (inputValue === false) return false;

                    if (inputValue === "") {
                        swal.showInputError("您没有输入交易密码");
                        return false
                    }
                    /*if($('.ts-order-Btn').hasClass('enable_pay') ) {
                     return;
                     }*/
                    //$('.ts-order-Btn').addClass('enable_pay');
                    //$(this).removeAttrs('id');
                    var crypt = new JSEncrypt();
                    var key   = $('#key').val();
                    crypt.setKey(key);
                    var enc = crypt.encrypt(inputValue);
                    $.post('/subscription/pay', {'trade_pwd':enc}, function(result){
                        if(result.code != 200) {
                            if(result.data == '您还没有设置交易密码,请前往用户管理中心>账户设置进行设置') {
                                swal.showInputError("您还没有设置交易密码,<a href='/member/resetTradePassword'>点击此处</a>进行设置");
                            } else {
                                swal.showInputError(result.data);
                            }
//                            pwdDialog();
                        } else {
                            swal({
                                title: "",
                                text:"购买成功",
                                type: "success",
                                confirmButtonText: "确定",
                            })
                            setTimeout(function(){
                                location.href = '/member/subscription';
                            }, 1000);
                        }
                    });
                });


        };
        $('#pay').click(pwdDialog)
    })
    $(".recharge").click(function(){
        $(this).attr('target','_blank');
    });

</script>
