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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{json_encode($data)}}" id="data">
</div>
<div style="width: 100%;height: 4028px;background-color: black;opacity: 0.5;display: none;position: absolute;top: 0;z-index: 999;" id="mask">
&nbsp;
</div>
@include("front.layouts.foot")
<script>
    $(function () {

        var data = $('#data').val()
        console.log(data)
        console.log(JSON.parse(data))
        data = JSON.parse(data)
        if (data.code != 200){
            swal({
                    title: "提示",
                    text: data.data,
                    type: "info",
                    showCancelButton: false,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: false
                },
                function(){
                    location.href = '/member/recharge'
                });
        }

        function onBridgeReady(){
            console.log('weixin js is ready')

            WeixinJSBridge.invoke('getBrandWCPayRequest', data.data, function(res) {
                switch(res.err_msg) {
                    case 'get_brand_wcpay_request:cancel':
                        swal('提示', '用户取消支付！',"error");
                        break;
                    case 'get_brand_wcpay_request:fail':
                        swal('提示', '支付失败！（'+res.err_desc+'）',"error");
                        break;
                    case 'get_brand_wcpay_request:ok':
                        swal({
                                title: "成功",
                                text: "支付成功",
                                type: "info",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: false,
                            },
                            function(){
                                location.href = '/member'
                            });
                        break;
                    default:
                        swal('提示', '支付失败！',"error");
                        break;
                }
                location.href="/member/recharge"
            });

        }
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        }else{
            onBridgeReady();
        }

    })
</script>