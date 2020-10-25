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
                                <div class="nine wide column">
                                    <div class="ui inverted divided equal height grid">

                                        <div class="ui labeled button" tabindex="0">
                                            <div class="ui blue button">
                                                提货楼盘
                                            </div>
                                            <a class="ui basic left pointing blue label">
                                                {{$asset->project->name}}
                                            </a>
                                        </div>
                                        <div class="ui labeled button" tabindex="0">
                                            <div class="ui red button">
                                                可提数量
                                            </div>
                                            <a class="ui basic red left pointing label">
                                                {{intval($asset->amount)}}
                                            </a>
                                        </div>
                                        <div class="ui labeled button" tabindex="0">
                                            <div  class="ui yellow button">
                                                提货规则
                                            </div>
                                            <a class="ui basic red left pointing label">
                                                按{{$asset->project->rule . $asset->project->rule_desc}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="userMain">
                            <form class="ui form" id="withdrawForm">
                                <p class="msg_error"><b></b></p>
                                <div class="field">
                                    <input name="amount" id="amount" type="number" min="{{$asset->project->rule}}" step="{{$asset->project->rule}}" placeholder="请输入您要提货的数量">
                                </div>
                                <div class="field">
                                    <input name="name" id="name" type="text" placeholder="请输入收货人姓名">
                                </div>
                                <div class="field">
                                    <select name="province" id="province" class="ui search dropdown"></select>
                                </div>
                                <div class="field">
                                    <select name="city" id="city" class="ui search dropdown"></select>
                                </div>
                                <div class="field">
                                    <select name="area" id="area" class="ui search dropdown"></select>
                                </div>
                                <div class="field">
                                    <input name="addr" id="addr" autocomplete="flase" value="" type="text" placeholder="请输入收货地址">
                                </div>
                                <div class="field">
                                    <input name="phone" id="phone" autocomplete="flase" value="" type="text" placeholder="请输入收货人手机号">
                                </div>
                                <div class="field">
                                    {{--<input style="position: absolute; top: -1000px !important; height: 0px;" name="tradePassword1" value="" id="tradePassword1" type="password">--}}
                                    <input name="tradePassword" value="" id="tradePassword" type="password" placeholder="请输入您的交易密码">
                                </div>
                                <div class="field">
                                    <p>为保证您顺利收到楼盘，请正确填写收货人信息。提货产生的运费由会员在签收时自行承担，请您在收货时做好准备。</p>
                                </div>
                                <input type="hidden" value="{{$asset->id}}" id="asset_id">
                                <input id="submit" type="submit" class="ui orange submit button right formBtn" value='确定'>
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

    $(function () {
        var addrs
        var delivery_rule = {{$asset->project->rule}}
        $.getJSON('/js/address3.json', {}, function (res) {
            addrs = res;
            var provinces = "<option value=''>选择省份</option>>";
            for ( var p in addrs) {
                provinces += "<option value='" + p + "'>" + p +"</option>"
            }
            $('#province').html(provinces)
            $('#province').on('change', function () {
                $('#area').html('');
                var province = $(this).val()

                if (province == '') {
                    $('#city').html('');
                }else{
                    var cities = "<option value=''>选择城市</option>>";
                    for (var c in addrs[province]) {
                        cities += "<option value='" + c + "'>" + c +"</option>"
                    }
                    $('#city').html(cities)
                }
            })
            $('#city').on('change', function () {
                var province = $('#province').val()
                var city = $(this).val()
                if (city == '') {
                    $('#area').html('');
                }else{
                    var areas = "<option value=''>选择区/县</option>>";
                    for (var a in addrs[province][city]) {
                        areas += "<option value='" +  addrs[province][city][a] + "'>" +  addrs[province][city][a] +"</option>"
                    }
                    $('#area').html(areas)
                }
            })

        });

        $('#withdrawForm').on('submit', function (e) {
            e.preventDefault()
            $(this).addClass('disabled')
            var _this = $(this)
            var amount = $('#amount').val()
            var name = $('#name').val()
            var province = $('#province').val()
            var city = $('#city').val()
            var area = $('#area').val()
            var addr = $('#addr').val()
            var phone = $('#phone').val()
            var asset_id = $('#asset_id').val()

            if(!amount) {
                msg_error('请输入您要提货的数量');
                return false;
            }

            if(amount < delivery_rule || amount % delivery_rule != 0) {
                return msg_error('请以' + delivery_rule + '的倍数提货')
            }

            var validate_rules = {
                'name': '',
                'province': '请选择收件省份',
                'city': '请选择收件城市',
                'area': '请选择收件区/县',
                'addr': '请填写收件地址',
                'phone': '请填写收件人电话'
            }
            if(!name) return msg_error('请输入收件人姓名');
            if(!province) return msg_error('请输入收件城市');
            if(!city) return msg_error('请输入收件城市');
            if(!area) return msg_error('请输入收件人区/县');
            if(!addr) return msg_error('请输入收件人地址');
            if(!phone || !/[0-9]/.test(phone)) return msg_error('请填写正确的手机号');

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
                return false
            }
            var tradePassword = crypt.encrypt(old);
            $('#submit').prop('disabled', true);
            $.post("/member/delivery", {
                tradePassword: tradePassword,
                amount: amount,
                name: name,
                province: province,
                city: city,
                area: area,
                addr: addr,
                phone: phone,
                asset_id: asset_id
            }, function (res) {
                if(res.code != 200) {
                    swal({
                        title: "",
                        text:res.data,
                        type: "error",
                        confirmButtonText: '确定',
                    }, function () {
                    })
                    return false;
                }
                swal({
                    title: "",
                    text:res.data,
                    type: "success",
                    confirmButtonText: "确定",
                }, function () {
                    window.location.href = '/member/deliveries';
                })
            }).fail(function (err) {
                swal({
                    title: "",
                    text: "服务器异常，请稍等重试",
                    type: "success",
                    confirmButtonText: '确定',
                }, function () {
                })
            }).always(function () {
                _this.removeClass('disabled')
                $('#submit').prop('disabled', false);
            });
            return false;
        })
    })
</script>