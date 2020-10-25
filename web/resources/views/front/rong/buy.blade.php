@section('title', '艺融宝')
@include("front.rong.head")

<style>
    .productDetail {
        width: 100%;
        margin-top: 20px;
    }
    .productDetail .desc{
        padding: 10px;
        color: #00a0e9;
        font-size:16px;
    }
    .productDetail .padd-left{
        padding-left: 10px;
    }
</style>

<div class="productDetail rong">
    <div>
        <img width="100%" src="{{asset('storage'). '/' .$model->banner}}" />
    </div>

    <div id="form" class="flex_table">
        <table class="ui very basic celled ">
            <thead class="ttitle">
            <td></td>
            <td>购买价格价格</td>
            <td >年化收益率</td>
            <td >期限</td>
            <td >余额</td>
            <td >可购买数量</td>
            </thead>
            <tbody>
                <tr>
                    <td>{{$model->name}}</td>
                    <td>{{$model->price}}</td>
                    <td>{{$model->rate * 100}}%</td>
                    <td>{{$model->duration}}个月</td>
                    <td>{{$balance}}</td>
                    <td>{{$canBuy}}</td>
                </tr>
            </tbody>
        </table>
        <table class="ui very basic celled second">
            <tr>
                <td>名称</td>
                <td>{{$model->name}}</td>
            </tr>
            <tr>
                <td>价格</td>
                <td>{{$model->price}}</td>
            </tr>
            <tr>
                <td>年化收益率</td>
                <td>{{$model->rate * 100}}%</td>
            </tr>
            <tr>
                <td>期限</td>
                <td>{{$model->duration}}个月</td>
            </tr>
            <tr>
                <td>余额</td>
                <td>{{$balance}}</td>
            </tr>
            <tr>
                <td>可买数量</td>
                <td>{{$canBuy}}</td>
            </tr>
            </tbody>
        </table>
        <form class="ui form">
            <div class="field">
                <input type="password" id="tradePassword1" style="height: 0; width: 0; position: absolute; top: -10000px;" name="tradePassword1" placeholder="输入交易密码">
                <input type="hidden" id="id" name="id" value="{{$model->id}}" placeholder="输入购买数量">
                <input type="number" id="amount" name="amount" placeholder="输入购买数量">
            </div>
            <div class="field">
                <input type="password" id="tradePassword" name="tradePassword" placeholder="输入交易密码">
            </div>
            <div class="field padd-left">
                <div class="ui checkbox">
                    <input type="checkbox" name="agreen" id="agreen" tabindex="0">
                    <label>本人同意并接受<a target="_blank" href="/rong/protocol">《北京益通云文化有限公司艺融宝固定收益产品数字化购买协议》</a></label>
                </div>
            </div>
            <div class="padd-left">
                <button class="ui button" id="submit">提交</button>
            </div>
        </form>
    </div>
    <textarea id="key" style="display: none">{!! $key !!}</textarea>
</div>
<script>
    $(function () {
        $('#submit').on('click', function (e) {
            e.preventDefault()

            if(!document.getElementById('agreen').checked) {
                return rongWarning('请先阅读《购买条款和协议》')
            }

            var amount = $('#amount').val()

            if(!amount || amount <= 0) {
                return rongWarning("购买数量不可为空")
            }

            if(!/^(-?\d+)(\.\d+)?$/.test(amount)) {
                return rongWarning("购买数量不可为空")
            }

            var crypt = new JSEncrypt();
            var key   = $('#key').val();
            crypt.setKey(key);
            var old = $('#tradePassword').val();
            if(!old) {
                return rongWarning('请输入您的交易密码')
            }
            var tradePassword = crypt.encrypt(old);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });

            $.post("/rong/buy", {
                id: $('#id').val(),
                amount: amount,
                tradePassword: tradePassword
            }, function (res) {

                if(res.code !== 200) {
                    return rongWarning(res.data)
                }

                rongSuccess("购买成功", function () {
                    window.location.href = '/rong#my-pro'
                })
            });
        })
    })

</script>

@include("front.rong.foot")
