@section('title', '提现 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                        <div class="userMain">
                            <form class="ui form" id="withdrawForm">
                                <p class="msg_error"><b></b></p>
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
                                    <p>为保证您顺利收到楼盘，请正确填写收货人信息。提货产生的运费由会员在签收时自行承担，请您在收货时做好准备。</p>
                                </div>
                                <input id="submit" type="submit" class="ui orange submit button right formBtn" value='确定'>
                            </form>
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
            var name = $('#name').val()
            var province = $('#province').val()
            var city = $('#city').val()
            var area = $('#area').val()
            var addr = $('#addr').val()
            var phone = $('#phone').val()

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

            $('#submit').prop('disabled', true);
            $.post("/addr/edit", {
                name: name,
                province: province,
                city: city,
                area: area,
                addr: addr,
                phone: phone,
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
                    window.location.href = '/{{$prev_action}}';
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