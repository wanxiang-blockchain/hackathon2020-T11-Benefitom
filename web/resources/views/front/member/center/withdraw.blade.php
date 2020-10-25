@section('title', '提现 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column withdraw">
                        <div class="userTop">
                            <div class="ui stackable inverted divided equal height stackable grid">
                                <div class="seven wide column">
                                    <h3>{{number_format($total_amount, 2)}}qcash</h3>
                                    <h4 class="ui header balance">
                                        <img class="ui image" src="{{asset('front/image/money_icon.png')}}">
                                        <div class="content">可用余额</div>
                                    </h4>
                                </div>
                                <div class="nine wide column">
                                    <div class="ui inverted divided equal height grid">
                                        <div class="seven wide column">
                                            <a href="{{route('member/recharge')}}" class="rechargeBtn">充值</a>
                                        </div>
                                        <div class="seven wide column">
                                            <a href="{{route('member/withdraw')}}" class="rechargeBtn active">提现</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="userMain">
                            <p>您当前的账户余额：{{number_format($balance,2)}}qcash</p>
                            {{--<p>--}}
                                {{--提现功能，正在路上，敬请期待...--}}
                            {{--</p>--}}
                            <form class="ui form" id="withdrawForm">
                                    <p class="msg_error"><b></b></p>
                                    <div class="field">
                                        <input name="money" id="withdrawAmount" type="number" min="1" step="0.01" placeholder="请输入您要提现的金额">
                                    </div>
                                    <div class="field">
                                        <input name="payment" id="withdrawId" type="text" placeholder="请输入您的支付宝账号">
                                    </div>
                                    <div class="field">
                                        <input name="aliname" id="aliname" autocomplete="flase" value="" type="text" placeholder="请输入您的支付宝实名姓名">
                                    </div>
                                    <div class="field">
                                        <input name="tradePassword" value="" id="tradePassword" type="password" placeholder="请输入您的交易密码">
                                    </div>
                                    <div class="field">
                                        <p>为保证您的资金完全到达您的账户，请您务必正确填写您的支付宝账号和支付宝实名姓名。</p>
                                    </div>
                                    <div class="field" style="margin-bottom:50px;">
                                        <i class="selected radio icon"></i>支付宝提现
                                        <div style="color: #3e3e3e; font-size: 10px;" class="ui icon" data-tooltip="提现按 0.15% 收取，不足2qcash按2qcash算，超出25qcash，按25qcash算。">了解手续费<i class="help circle icon"></i> </div>
                                    </div>
                                <input type="button" id="submit" class="ui orange submit button right formBtn" value='确定'>
                            </form>
                            <textarea id="key" style="display: none">{!! $key !!}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
<script>
    $('#submit').on('click', function () {
        $('#submit').prop('disabled', true);
        var money = $('#withdrawAmount').val();
        var payment = $('#withdrawId').val();
        var aliname = $('#aliname').val();
        if(!money) {
            msg_error('请输入您要提现的金额');
            $('#submit').prop('disabled', false);
            return false;
        }
        if(!payment) {
            msg_error(' 请输入您的支付宝账号');
            $('#submit').prop('disabled', false);
            return false;
        }
        if(!aliname) {
            msg_error(' 请输入您的支付宝用户名');
            $('#submit').prop('disabled', false);
            return false;
        }
        if(!/^(-?\d+)(\.\d+)?$/.test(money)) {
            msg_error(' 请输入正确的金额');
            $('#submit').prop('disabled', false);
            return false;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var old = $('#tradePassword').val();
        if(!old) {
            msg_error('请输入您的交易密码')
            $('#submit').prop('disabled', false);
            return false
        }
        var tradePassword = crypt.encrypt(old);
        var fee = money * 0.0015;
        if(fee < 2) fee = 2;
        if(fee > 25) fee = 25;
        swal({
            title: "提现!",
            text: "<p>提现<span style='color:#F8BB86'>" + money + "<span>qcash</p>" +
                  "<p>手续费<span style='color:#F8BB86'>" + fee + "<span>qcash</p>" +
                  "<p>实际到账<span style='color:#F8BB86'>" + (money - fee) + "<span>qcash</p>",
            html: true,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
            $.post("/member/postWithDraw", {money:money, payment:payment, tradePassword: tradePassword, aliname: aliname}, function (res) {
                if(res.code != 200) {
                    swal({
                        title: "",
                        text:res.data,
                        type: "warning",
                        confirmButtonText: "确定",
                        closeOnConfirm: false
                    })
                }else{
                    swal({
                        title: "",
                        text:res.data,
                        type: "success",
                        confirmButtonText: "确定",
                    }, function () {
                        window.location.reload();
                    })
                }
            });
        })
        $('#submit').prop('disabled', false);
    })
</script>