@section('title', '充值中心 - 用户管理中心')
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
                                <div style="text-align: center;" class="seven wide column">
                                    <h3>{{number_format($total_amount, 2)}}qcash</h3>
                                    <h4 class="ui header balance">
                                        <img class="ui image" src="{{asset('front/image/money_icon.png')}}">
                                        <div class="content">人民币现金账户管理</div>
                                    </h4>
                                </div>
                                <div class="nine wide column">
                                    <div class="ui inverted divided equal height grid">
                                         <div class="seven wide column">
                                            <a href="{{route('member/recharge')}}" class="rechargeBtn active">现金充值</a>
                                        </div>
                                        <div class="seven wide column">
                                            <a href="{{route('member/withdraw')}}" class="rechargeBtn">现金提现</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="userMain">
                            <p>您当前的现金账户余额：{{number_format($balance, 2)}}qcash</p>
                            @if(Config::get('app.env') != 'test')
                            <form class="ui form" id="rechangeForm" action="{{route('pay/getRecharge')}}" onsubmit="mask()">
                                <div class="field">
                                    <input name="rechangeAmount" id="rechangeAmount" type="number" required min="0.01" step="0.01" placeholder="请输入您要充值的金额">
                                </div>
                                <div class="field" style="margin-bottom:50px;">
                                    <i class="selected radio icon"></i>
                                    支付宝支付
                                </div>
                                <input type="submit" id="submit" class="ui orange submit button right formBtn" value='支付'>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div style="width: 100%;height: 4028px;background-color: black;opacity: 0.5;display: none;position: absolute;top: 0;z-index: 999;" id="mask">
&nbsp;
</div>
@include("front.layouts.foot")
<script>
    var id = '';
    function mask() {
        $('#mask').show();
    }
    $('#mask').on('click', function () {
       $(this).hide();
    });
    $('#submit').on('click', function () {
        if($(this).hasClass('disabled')) {
            return false;
        }
        if($('#rechangeAmount').val()) {
            $(this).addClass('disabled');
            window.setInterval("ajax_check()", 5000);
        }
    })
    function ajax_check() {
        $.get('/member/recharge?op=ajax&log_id='+id, function (res) {
            if(res.code==200) {
                window.location.reload();
            } else {
                id = res.data.id;
            }
        })
    }
</script>