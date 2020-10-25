<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<a href="javascript:void (0);" id="day" style="color: #333">日线图</a>
<a href="javascript:void (0);" id="hour" style="color: #333">时线图</a>
<div id="containers" style="height: 420px !important;margin: 0;"></div>
<script type="text/javascript">
    //异步加载数据
    var code = '{!! isset($code) ? $code : $asset_type->code !!}';
    var title = '';
    var title_line = ['K线', '5日', '5分', '15分', '30分', '60分'];
    var data0 = {categoryData:[],values:[]};
    option = null;
    var ajax_params = :code, type:1};
    var ajax_data = [];{code
    var option = {
        title: {
            text: title,
            left: 'center'
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: false},
                dataZoom : {show: false},
                dataView : {show: false, readOnly: false},
                magicType: {show: false, type: ['line', 'bar']},
                restore : {show: true},
                saveAsImage : {show: false}
            }
        },
        legend: {
            data: title_line
        },
        grid: {
            left: '10%',
            right: '10%',
            bottom: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: data0.categoryData,
            scale: true,
            boundaryGap : false,
            axisLine: {onZero: false},
            splitLine: {show: false},
            //splitNumber: 20,
            min: 'dataMin',
            max: 'dataMax'
        },
        yAxis: {
            scale: true,
            splitArea: {
                show: false
            },
            splitLine: {show:false},
            name:'价格 (RMB)',
        },
        dataZoom: [
            {
                type: 'inside',
//                start: 0,
//                end: 95
            },
            {
                show: true,
                type: 'slider',
                y: '90%',
                start: 0,
                end: 90,
                dataBackground:{
                    lineStyle:{
                        color:"rgb(49, 70, 86)",
                        width:1,
                        opacity:1,
                    },
                    areaStyle:{
                        color:"rgb(49, 70, 86)",
                        opacity:1,

                    }
                }
            }
        ],
        series: [
            {
                ///name: 'K线',
                type: 'candlestick',
                data: data0.values,
                ItemStyle:{
                    normal: {
                        color: 'red',
                        color0: 'lightgreen"'
                    }
                }

            },
        ]
    };
    var myChart = echarts.init(document.getElementById('containers'));

    //组装数据
    function splitData(rawData) {
        var categoryData = [];
        var values = [];
        for (var i = 0; i < rawData.length; i++) {
            categoryData.push(rawData[i].splice(0, 1)[0]);
            values.push(rawData[i])
        }
        data0.categoryData = categoryData;
        data0.values = values;
        return {
            categoryData: categoryData,
            values: values
        };
    }

    //计算 MA 值
    function calculateMA(dayCount) {
        var result = [];
        for (var i = 0, len = data0.values.length; i < len; i++) {
            if (i < dayCount) {
                result.push('-');
                continue;
            }
            var sum = 0;
            for (var j = 0; j < dayCount; j++) {
                sum += data0.values[i - j][1];
            }
            var new_price = sum / dayCount;
            result.push(new_price.toFixed(2));
        }
        return result;
    }

    //异步请求数据
    function fetchData(cb) {
        setTimeout(function () {
            $.get('/member/Kchart', ajax_params, function (data) {
                ajax_data = data;
                var new_data = splitData(data.params);
                cb(new_data);
            });
        }, 800);
    }
    //设置参数
    function setNewOption(data) {
        var _k_params = ajax_data._params;
        myChart.setOption(
            {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                    },
                    formatter: function (params) {
                        var res = params[0].seriesName + ' ' + params[0].name;
                        var type = params[0].componentSubType;
                        if(type == "candlestick") {
                            res += '<br/>  开盘 : ' + params[0].value[0] + '  最高 : ' + params[0].value[3];
                            res += '<br/>  收盘 : ' + params[0].value[1] + '  最低 : ' + params[0].value[2];
                            res += '<br/>  成交额 : ' + _k_params[params[0].axisValue][0] + '  成交量 : ' + _k_params[params[0].axisValue][1]+'<br/>';
                            if(params.length >= 2) {
                                if (!isNaN(params[1].value)) {
                                    res += params[1].seriesName + ':' + params[1].value + '<br/>';
                                } else {
                                    res += params[1].seriesName + ':-<br/>';
                                }
                            }
                            if(params.length >= 3) {
                                if (!isNaN(params[2].value)) {
                                    res += params[2].seriesName + ':' + params[2].value + '<br/>';
                                } else {
                                    res += params[2].seriesName + ':-<br/>';
                                }
                            }
                            if(params.length >= 4) {
                                if (!isNaN(params[3].value)) {
                                    res += params[3].seriesName + ':' + params[3].value + '<br/>';
                                } else {
                                    res += params[3].seriesName + ':-<br/>';
                                }
                            }
                            if(params.length >= 5) {
                                if (!isNaN(params[4].value)) {
                                    res += params[4].seriesName + ':' + params[4].value + '<br/>';
                                } else {
                                    res += params[4].seriesName + ':-<br/>';
                                }
                            }
                        } else {
                            var res = params[0].seriesName +':'+ params[0].value+'<br/>';
                        }
                        return res;
                    }
                },
                xAxis: {
                    data:data.categoryData,
                    axisTick: {onGap:false},
                    splitLine: {show:false},
                },
                series: [{
                    // 根据名字对应到相应的系列
                    name:'K线',
                    data: data.values,
                },
                    {
                        name: 'MA5',
                        type: 'line',
                        data: calculateMA(5),
                        smooth: true,
                        lineStyle: {
                            normal: {opacity: 0.5}
                        }
                    },
                    {
                        name: 'MA10',
                        type: 'line',
                        data: calculateMA(10),
                        smooth: true,
                        lineStyle: {
                            normal: {opacity: 0.5}
                        }
                    },
                    {
                        name: 'MA20',
                        type: 'line',
                        data: calculateMA(20),
                        smooth: true,
                        lineStyle: {
                            normal: {opacity: 0.5}
                        }
                    },
                    {
                        name: 'MA30',
                        type: 'line',
                        data: calculateMA(30),
                        smooth: true,
                        lineStyle: {
                            normal: {opacity: 0.5}
                        }
                    }
                ],
            }
        );
    }

    //默认日线图
    ajax_params.type=1;
    fetchData(function (data) {
        setNewOption(data);
    });
    $('#day').on('click', function () {
        ajax_params.type = 1;
        fetchData(function (data) {
            setNewOption(data);
        });
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
    });
    //分时图
    $('#hour').on('click', function () {
        ajax_params.type = 2;
        fetchData(function (data) {
            setNewOption(data);
        });
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
    });

    //设置显示图
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
</script>