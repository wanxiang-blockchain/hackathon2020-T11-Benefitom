@section('title', '益通云认购中心')
@include("front.layouts.head")
<div class="buyDetails pusher">
    <div class="ui container">
        <div class="ui stackable inverted grid">
            <div class="twelve wide column">
                <div class="ui items">
                    <div class="item">
                        <div class="image">
                            <img class="ui middle aligned tiny image" src="{{asset('storage/'.$project->picture)}}">
                        </div>
                        <div class="content">
                            <a class="header showPopup"  data-variation="very wide">{{$project->name}}</a>
                            <div class="buyDetailsOther">
                                <p>楼盘代码：{{$project->asset_code}}</p>
                                <p>楼盘单价：{{$project->price}}qcash/{{$project->price_unit}}</p>
                                <p>发行数量：{{$project->total . $project->price_unit}}</p>
                                <p>专营机构：{{$project->agent}}</p>
                            </div>
                            <div class="progressCon">
                                <p>发行进度</p>
                                <div class="ui progress" data-percent="50">
                                    <div class="bar"
                                         style="transition-duration:300ms;width:{{$project->progress}}%;"></div>
                                </div>
                                <input type="hidden" id="refreshed" value="no">
                                <span>{{$project->progress}}%</span>
                            </div>
                            <div class="buyDetailsOther">
                                <p>下架时间：{{$project->end}}</p>
                                <p>累认数量：{{$project->sold()}}</p>
                                <p>累认次数：{{$project->orderCount}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            @if( !(date('Y-m-d',time()) < $project->start || $project->limit == $project->position || date('Y-m-d',time()) > $project->end))--}}
                <div class="four wide column copies">
                    <form id="order" action="/subscription/need" method="post">
                        <p>认购数量</p>
                        {{csrf_field()}}
                        <textarea id="key" style="display: none">{!! $key !!}</textarea>
                        <input type="hidden" name="id" value="{{$project->id}}">
                        <input type="hidden" name="trade_pwd" value="">
                        <div class="ts-stock">
                            <a href="javascript:void(0);" class="tb-iconfont ts-reduce">-</a>
                            {{ Form::token() }}
                            {{ Form::hidden("id", $project->id) }}
                            <input type="text" name="amount" id="buyAmount" class="ts-text" value="{!! $sub_num !!}">
                            <a href="javascript:void(0);" class="tb-iconfont ts-increase">+</a>
                        </div>
                        @if(Auth::guard('front')->check())
                        <span class="ts-amount">确认金额：<span class="allMoney">{{$check_money}}</span>qcash</span>
                        <span class="ts-buyBalance">当前余额：<span class="balanceMoney">{{$finance}}</span>qcash</span>
                        <span @if(($finance - $check_money)>=0) style="display: none;"  @endif class="ts-needCharge">还需充值：<span class="needChargeMoney">{!! ($finance - $check_money)>0 ? 0 :number_format($check_money - $finance ,2, '.', '') !!}</span>qcash</span>
                            @else
                            <span class="ts-amount"></span>
                            <span class="ts-buyBalance"></span>
                            <span class="ts-needCharge"></span>
                        @endif
                        @if( (date('Y-m-d H:i:s',time()) < $project->start))
                            <a  href="javascript:void(0);" class="ts-LinkBuy enable_pay">即将开始...</a>
                        @elseif($project->limit == $project->position && date('Y-m-d H:i:s',time()) < $project->end)
                            <a  href="javascript:void(0);" class="ts-LinkBuy enable_pay">已售完</a>
                        @elseif(date('Y-m-d H:i:s',time()) > $project->end)
                            <a  href="{{route("trade/detail",['id' => $project->assetType->id])}}" class="ts-LinkBuy">去买入</a>
                        @else
                            @if(Auth::guard('front')->check())
                                @if($check_money - $finance > 0)
                                    <a  href="javascript:void(0);" class="ts-LinkBuy confirm_ok" > 立即充值</a>
                                @else
                                    <a  href="javascript:void(0);" class="ts-LinkBuy confirm_ok buy_comfirm">确认认购</a>
                                @endif
                            @else
                                <a  href="{{route('login')}}?back_type={{$project->id}}" class="ts-LinkBuy">去登录</a>
                            @endif
                        @endif
                        <div class="field buyAgreement">
                          <div class="ui checkbox {{request()->get('has_check')}}">
                            <input type="checkbox" tabindex="0" {{request()->get('has_check')}} class="hidden" name="agreement" id="agreement">
                            <label>本人同意并接受以下内容<a target="_blank" >《链英科技认购与交易协议》</a></label>
                          </div>
                        </div>
                    </form>
                </div>
            {{-- @endif--}}
        </div>
        <div class="itemsAbout">

            <div class="ui bottom attached tab segment active" data-tab="first">
                <div class="itemsIntro">
                    <div class="introTitle">
                        <h4 class="ui horizontal divider header">
                            <i class="play icon"></i>
                            产品介绍
                        </h4>
                    </div>
                    <p>
                        {!! $project->desc !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 充值弹窗 -->
<div class="ui small modal goChargeModal buyDetailsModalCom">
   <div class="content">
     <form class="ui form" id="rechangeFormModal" action="{{route('pay/getRecharge')}}">
       <div class="field">
           <input type="hidden" name="back_type" value="{{$project->id}}">
           <input type="hidden" name="sub_num" value="">
           <input type="hidden" name="has_check" value="">
         <input name="rechangeAmount" id="rechangeAmount" type="text" placeholder="请输入您要充值的金额">
       </div>
       <div class="field" style="margin-bottom:50px;">
         <i class="selected radio icon"></i>
         支付宝支付
       </div>
       <input type="submit" id="submit" class="ui orange submit button right formBtn" value='充值'>
     </form>
   </div>
   <div class="actions">
     <div class="ui negative button">取消</div>
   </div>
 </div>
<!-- 输入交易密码弹窗 -->
 <div class="ui small modal buyPasswordModal buyDetailsModalCom">
    <div class="content">
      <form class="ui form" id="tradePasswordModal" action="">
          @if($addr)
              <p>
                  收货地址：<span>{{$addr->province . $addr->city . $addr->area}}</span>
                  <span>{{$addr->name}}</span>
                  <span>{{$addr->phone}}</span>
                  收
              </p>
          @else
              <p>
                  <a href="/addr/index?prev_action={{urlencode('subscription/detail/' . $project->id)}}">您尚未设置收货地址，点此设置。</a>
              </p>
          @endif
        <div class="field">
        </div>
        <div class="field">
          <p>认购金额：<span class="sub_amount"></span>qcash</p>
          <p>认购份数：<span class="sub_num"></span>份</p>
        </div>
        <input type="button" id="check_subscription" class="ui orange submit button right formBtn" value='确认认购'>
      </form>
    </div>
    <div class="actions">
      <div class="ui negative button">取消</div>
    </div>
  </div>
<div style="width: 100%;height: 4028px;background-color: black;opacity: 0.5;display: none;position: absolute;top: 0;z-index: 999;" id="mask">
    &nbsp;
</div>
@push('endscripts')
<script src="/js/qweb3.js" type="text/javascript"></script>
<script src="/js/abi.js" type="text/javascript"></script>
<script type="text/javascript">
window.postMessage({ message: { type: 'CONNECT_QRYPTO' }}, '*');

 const contractAddress = '{{$project->contractAddress}}';

    var video  = $('#my-video')[0];
    var videoJ = $('#my-video');
    videoJ.on('ended', function(){
        video.load();
    });
    var balance=parseFloat($(".balanceMoney").text());
    var ts_text = $(".ts-text");
    //var m = $(".allMoney").text();
    var m = '{{$project->price}}';
    var allMoney = $(".allMoney");
    var buy = $('.canBuy').attr('buy');
    function updateMoney() {
        var inputVal = ts_text.val();
        var total = (inputVal * m).toFixed(2);
        allMoney.text(total);
        if (ts_text.val() <= 0) {
            allMoney.text(m);
        }
        if(parseFloat(allMoney.text())>balance){
            $('.confirm_ok').html('立即充值');
            $('.confirm_ok').removeClass('buy_comfirm');
            $('.confirm_ok').addClass('reduce');
            $('.confirm_ok').attr('onclick', 'reduce()');
            var needMoney = (parseFloat(allMoney.text())-balance).toFixed(2);
            if(needMoney>=0) {
                $('.ts-needCharge').show();
            }
          $(".needChargeMoney").text(needMoney);
        }else{
            $('.confirm_ok').html('确认认购');
            $('.confirm_ok').removeClass('reduce');
            $('.confirm_ok').addClass('buy_comfirm');
            $('.confirm_ok').attr('onclick', 'buy_comfirm()');
            $('.ts-needCharge').hide();
          $(".needChargeMoney").text(0);
        }
    }
    $(".ts-reduce").click(function () {
        ts_text.val(parseInt(ts_text.val()) - 1);
        if (ts_text.val() <= 0) {
            ts_text.val(parseInt(ts_text.val()) + 1);
        }
        updateMoney();
    });
    $(".ts-increase").click(function () {
        if(parseInt(ts_text.val()) >= parseInt(buy) || parseInt(ts_text.val()) >= "{{ $project->per_limit }}" ){
//            ts_text.val(parseInt(buy));
        }else{
            ts_text.val(parseInt(ts_text.val()) + 1);
            updateMoney();
        }
    });
    ts_text.on('blur',function () {
        var that = $(this);
        if(parseInt(that.val()) > parseInt(buy)){
            $(this).val(1);
        }
        if(that.val() <=0) {
            $(this).val(1);
        }
        updateMoney();
    });
    $(".ts-text").keyup(function () {
        var that = $(this);
        if(parseInt(that.val()) >= parseInt(buy)){
            that.val(parseInt(buy));
        }
        if (/[^\d]/.test(that.val())) {
            var amount = that.val().replace(/[^\d]/g, '');
            $(this).val(amount);
        }
        updateMoney();
    });
    $('.buy_comfirm').on('click', function(){
        var agreement = $('#agreement:checked').val();
        if(agreement != 'on') {
            swal({
                title: "",
                text:" 您还没有勾选同意 [认购与交易协议] ,请勾选后再认购",
                type: "info",
                confirmButtonText: "确定",
            }, function () {
            })
            return false;
        }
        $('.sub_num').html($('input[name=amount]').val());
        $('.sub_amount').html($('.allMoney').html());
        $('.buyPasswordModal.modal').modal('show');
        return false;
    })

    $('#check_subscription').on('click', async function () {
        if (qrypto.account.address == '') {
            swal({
                title: "",
                text:" 钱包已掉线，请重连",
                type: "info",
                confirmButtonText: "确定",
            }, function () {
            })
            return;
        }
        console.log("contract: ", contractAddress);
        const web3 = new qweb3.Qweb3(window.qrypto.rpcProvider);        
        var amount = $('input[name=amount]').val();
        var id = $('input[name=id]').val();
        const contract = web3.Contract(contractAddress, abi);
        const tx = await contract.send('buyTokens', {
            methodArgs: [100000000 * amount * m ],    // Sets the function params
            amount: 0,
            gasLimit: 200000,  // Sets the gas limit to 1 million
            gasPrice: 40,
            senderAddress: qrypto.account.address,
        });
        console.log(tx);
        if (tx == undefined || tx.txid == undefined) {
            swal({
                title: "",
                text:"转账失败",
                type: "info",
                confirmButtonText: "确定",
            }, function () {
            })
            return;
        }
        // todo 上报 tx 给服务端
        $.ajax({
            url:'{{route('subscription/qcashpay')}}',
            type:'post',
            data: {
                txid: tx.txid,
                amount: amount,
                id: id
            },
            dataType:'json',
            success:function (res) {
                if(res.code != 200) {
                    swal({
                        title: "",
                        text: res.data,
                        type: "info",
                        confirmButtonText: "确定",
                    }, function () {
                        //window.location.reload();
                    })
                    //location.href = '/subscription/subSuccess?type=2&num='+$('input[name=amount]').val()+'&amount='+$('.allMoney').html();
                } else {
                    location.href = '/subscription/subSuccess?type=1&num='+$('input[name=amount]').val()+'&amount='+$('.allMoney').html()+'&name={{$project->name}}&id={{$project->id}}&note=' + res.data;
                }
            },
            error:function () {

            }
        })
 

        // Create a new Contract instance and use the same provider as qweb3

        /**
        var crypt = new JSEncrypt();
        var key   = $('#key').val();
        crypt.setKey(key);
        var trade_pwd = $('#tradePassword').val();
        var trade_pwd_mi = crypt.encrypt(trade_pwd);
        //$('#tradePassword').val(trade_pwd_mi);
        $('input[name=trade_pwd]').val(trade_pwd_mi);
        $.ajax({
            url:'{{route('subscription/pay')}}',
            type:'post',
            data:$('#order').serialize(),
            dataType:'json',
            success:function (res) {
                if(res.code != 200) {
                    swal({
                        title: "",
                        text: res.data,
                        type: "info",
                        confirmButtonText: "确定",
                    }, function () {
                        //window.location.reload();
                    })
                    //location.href = '/subscription/subSuccess?type=2&num='+$('input[name=amount]').val()+'&amount='+$('.allMoney').html();
                } else {
                    location.href = '/subscription/subSuccess?type=1&num='+$('input[name=amount]').val()+'&amount='+$('.allMoney').html()+'&name={{$project->name}}&id={{$project->id}}&note=' + res.data;
                }
            },
            error:function () {

            }
        })
        **/
    });

    function GetRTime(){
        var ends = $('.time').attr('time')*1000;
        var EndTime= new Date(ends);
        var NowTime = new Date();
        var t =EndTime.getTime() - NowTime.getTime();
        if($('#t_d').html() == '已结束') {
            return false;
        }
        if(isNaN(t)) {
            $('#t_d').html('已结束');
            return false;
        }
        var d=Math.floor(t/1000/60/60/24);
        var h=Math.floor(t/1000/60/60%24);
        var m=Math.floor(t/1000/60%60);
        var s=Math.floor(t/1000%60);
        $('#t_d').html(d + "天" + h + "时" + m + "分" + s + "秒");
    }
    setInterval(GetRTime,0);
    //充值begain
    function reduce (){
        $('#rechangeAmount').val($('.needChargeMoney').html());
        $('input[name=sub_num]').val($('.ts-text').val());
        var check = $('#agreement:checked').val();
        if(check == 'on') {
            $('input[name=has_check]').val('checked');
        } else {
            $('input[name=has_check]').val('');
        }
        $('.goChargeModal.modal').modal('show');
    }
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
    //充值 end

    //后退刷新
    $(function(){
        var e=document.getElementById("refreshed");
        if(e.value=="no")e.value="yes";
        else{e.value="no";location.reload();}
    });
</script>
@endpush
@include("front.layouts.foot")
<script type="text/javascript">
  $('.showPopup').popup();
</script>
