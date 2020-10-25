@section('title', '提取 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                    <div class="thirteen wide column withdraw">
                        <div class="userMain">
                            <input type="hidden" id="balance" value="{{$balance}}">
                            <p>您当前的赠品账户ArTBC：{{$balance}}</p>
                            <form class="ui form" id="withdrawForm">
                                    <p class="msg_error"><b></b></p>
                                    <div class="field">
                                        <input name="amount" id="amount" type="number" min="0" placeholder="请输入您要提取的数量">
                                    </div>
                                    <div class="field">
                                        <input name="eth_addr" id="eth_addr" type="text" placeholder="请输入您的以太坊钱包地址 ">
                                    </div>
                                    <div class="field">
                                        <input name="tradePassword" value="" id="tradePassword" type="password" placeholder="请输入您的交易密码">
                                    </div>
                                    <div class="field">
                                        <p>以太坊交易打包会有延时，如24小时未到账请联系客服人员。</p>
                                    </div>
                                    <div class="field" style="margin-bottom:50px;">
                                        <div style="color: #3e3e3e; font-size: 10px;" class="ui icon" data-tooltip="提取按 0% 收取收续费，即提取100可到账100。">了解手续费<i class="help circle icon"></i> </div>
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
        var amount = $('#amount').val();
        var eth_addr = $('#eth_addr').val();
        if(!amount) {
            msg_error('请输入您要提取的数量');
            $('#submit').prop('disabled', false);
            return false;
        }
        if(!/^(-?\d+)(\.\d+)?$/.test(amount)) {
            msg_error(' 请输入正确的金额');
            $('#submit').prop('disabled', false);
            return false;
        }
        if(eth_addr.length != 42) {
            msg_error(' 钱包地址格式不正确!');
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
        var fee = 0;
        swal({
            title: "提取!",
            text: "<p>提取：<span style='color:#F8BB86'>" + amount + "</span> artbc</p>" +
                  "<p>手续费：<span style='color:#F8BB86'>" + fee + "</span> artbc</p>" +
                  "<p>实际到账：<span style='color:#F8BB86'>" + (amount - fee) + "</span> artbc</p>",
            html: true,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
            $.post("/member/artbc/ti", {amount:amount, eth_addr:eth_addr, tradePassword: tradePassword}, function (res) {
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