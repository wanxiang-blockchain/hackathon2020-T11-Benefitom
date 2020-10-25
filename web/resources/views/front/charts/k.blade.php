<?php
/**
 * K线图
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/7/10
 * Time: 下午5:42
 */
?>
<style>
    .k_button {
        padding: 5px !important;
        margin-bottom: 5px;
    }
</style>

<script type="text/javascript" src="/front/js/k.js?v=27"></script>
<input type="hidden" id="asset_type_k" value="{{$asset_type['code']}}">
<input type="hidden" id="asset_type_price_max" value="{{$asset_type['rise_limit']}}">
<input type="hidden" id="asset_type_price_min" value="{{$asset_type['fall_limit']}}">
<a href="javascript:void (0);" id="hour" data-type="1" class="ui button k_button red" >分时图</a>
<a href="javascript:void (0);" id="day" data-type="0" class="ui button k_button"  >日K</a>
<a href="javascript:void (0);" id="5m" data-type="5" class="ui button k_button" >5分</a>
<a href="javascript:void (0);" id="10m" data-type="10" class="ui button k_button" >10分</a>
<a href="javascript:void (0);" id="15m" data-type="15" class="ui button k_button" >15分</a>
<a href="javascript:void (0);" id="30m" data-type="30" class="ui button k_button" >30分</a>
<a href="javascript:void (0);" id="60m" data-type="60" class="ui button k_button" >60分</a>
<div id="containers" style="height: 420px !important;margin: 0;"></div>
<script>
    $(function () {

        window.ws_port = 9501
        if(location.host == 'trade.yigongpan.com') {
            window.ws_port = 9502
        }

        var assetType = $('#asset_type_k').val();
        var min = $('#asset_type_price_min').val()
        var max = $('#asset_type_price_max').val()

//        k_test();
//        k_render(assetType, 0);
        min_render(assetType, min, max);

        $('.k_button').on('click', function () {
            clearInterval(window.timerId);
            window.timerId = 0;
            if(window.myChart) {
                window.myChart.dispose()
            }
            $('.k_button').removeClass('red');
            $(this).addClass('red')
            var id = $(this).prop('id')

            var kType = $(this).data('type');
            switch (id){
                case 'hour':
                    min_render(assetType, min, max);
                    break;
                default:
                    k_render(assetType, kType);
            }
        })
    })

</script>