/**
 * Created by johnShaw on 17/7/11.
 */
/**
 * 渲染K线图
 * @param asset_type
 * @param type  day  5m 10m ...
 */
function k_render(asset_type, type) {

    var dates = [];
    var data = [];

    var redColor = '#ef232a'
    var greenColor = '#7fbe9e'

    function calculateMA(dayCount, data) {
        var result = [];
        for (var i = 0, len = data.length; i < len; i++) {
            if (i < dayCount) {
                result.push('-');
                continue;
            }
            var sum = 0;
            for (var j = 0; j < dayCount; j++) {
                sum += data[i - j][1];
            }
            result.push((sum / dayCount).toFixed(2));
        }
        return result;
    }

    var option = {
        animation: false,
        title: '',
        legend: {
            top: 50,
            data: ['日K', 'MA10', 'MA20', 'MA30', 'MA60']
        },
        tooltip: {
            trigger: 'axis',
            triggerOn: 'mousemove|click',
            transitionDuration: 0,
            confine: true,
            bordeRadius: 4,
            borderWidth: 1,
            borderColor: '#333',
            backgroundColor: 'rgba(255,255,255,0.9)',
            textStyle: {
                fontSize: 12,
                color: '#333'
            },
            position: function (pos, params, el, elRect, size) {
                var obj = {
                    top: 60
                };
                obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 5;
                return obj;
            },
            formatter: function (params) {
                if(params[0].componentSubType == "custom") {
                    // 如果是查看蜡烛线
                    var candle = params[0];
                    var bar = params[5]
                } else {
                    var candle = params[1];
                    var bar = params[0]
                }
                var res = candle.seriesName + ' ' + candle.axisValue;
                res += '<br/>  开盘 : ' + candle.value[0] + '  最高 : ' + candle.value[3];
                res += '<br/>  收盘 : ' + candle.value[1] + '  最低 : ' + candle.value[2];
                res += '<br /> 成交量 : ' + bar.value
                return res;
            }

        },
        axisPointer: {
            link: [{
                xAxisIndex: [0, 1]
            }]
        },
        dataZoom: [{
            type: 'slider',
            xAxisIndex: [0, 1],
            realtime: true,
            top: 20,
            height: 20,
            handleIcon: 'M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
            handleSize: '120%'
        }, {
            type: 'inside',
            xAxisIndex: [0, 1],
            realtime: true,
            top: 30,
            height: 20
        }],
        xAxis: [{
            type: 'category',
            data: [],
            boundaryGap : false,
            axisLine: { lineStyle: { color: '#777' } },
            min: 'dataMin',
            max: 'dataMax',
            axisPointer: {
                show: true
            }
        }, {
            type: 'category',
            gridIndex: 1,
            data: [],
            scale: true,
            boundaryGap : false,
            splitLine: {show: false},
            axisLabel: {show: false},
            axisTick: {show: false},
            axisLine: { lineStyle: { color: '#777' } },
            min: 'dataMin',
            max: 'dataMax',
        }],
        yAxis: [{
            scale: true,
            splitNumber: 4,
            min: 'dataMin',
            max: 'dataMax',
            axisLine: { lineStyle: { color: '#777' } },
            splitLine: { show: true },
            axisTick: { show: false },
            axisLabel: {
                inside: true,
                formatter: '{value}\n'
            },

        }, {
            scale: true,
            gridIndex: 1,
            splitNumber: 4,
            min: 0,
            max: 'dataMax',
            axisLabel: {show: false},
            axisLine: {show: false},
            axisTick: {show: false},
            splitLine: {show: false}
        }],
        grid: [{
            left: 20,
            right: 20,
            top: 110,
            height: 180
        }, {
            left: 20,
            right: 20,
            height: 60,
            top: 320
        }],
        series: [{
            name: '成交量',
            type: 'bar',
            xAxisIndex: 1,
            yAxisIndex: 1,
            itemStyle: {
                normal: {
                    color: function (params) {
                        return window.barColorList[params.name] ? window.barColorList[params.name] : redColor
                    }
                },
                emphasis: {
                    color: '#140'
                }
            },
            data: []
        }, {
            name: '日K',
            type: 'custom',
            renderItem: function (parames, api) {
                var xValue = api.value(5);
                var openVal = api.value(0);
                var closeVal = api.value(1);
                var open = api.coord([xValue, openVal]);
                var close = api.coord([xValue, closeVal]);
                var highest = api.coord([xValue, api.value(2)]);
                var lowest = api.coord([xValue, api.value(3)]);
                var halfWidth = api.size([1, 0])[0] * 0.25;

                // 自己定义颜色吧
                var color = openVal > closeVal ? 'green' : 'red';

                return {
                    type: 'group',
                    children: [{
                        type: 'line',
                        shape: {
                            x1: highest[0],
                            y1: highest[1],
                            x2: lowest[0],
                            y2: lowest[1],
                        },
                        style: api.style({
                            stroke: color,
                            fill: null
                        })
                    }, {
                        type: 'rect',
                        shape: {
                            x: open[0] - halfWidth,
                            y: Math.min(open[1], close[1]),
                            width: halfWidth * 2,
                            height: Math.abs(close[1] - open[1]) == 0 ? 1 : Math.abs(close[1] - open[1])
                        },
                        style: api.style({
                            stroke: null,
                            fill: color
                        })
                    }]
                };
            },
            dimensions: ['open', 'close', 'highest', 'lowest', null, null],
            encode: {
                x: 5,
                y: [0, 1, 2, 3],
                tooltip: [0, 1, 2, 3]
            },
            data: [],
            itemStyle: {
                normal: {
                    color: redColor,
                    color0: greenColor,
                    borderColor: redColor,
                    borderColor0: greenColor,
                },
                emphasis: {
                    color: 'black',
                    color0: '#444',
                    borderColor: 'black',
                    borderColor0: '#444'
                }
            }
        }, {
            name: 'MA10',
            type: 'line',
            data: [],
            smooth: true,
            showSymbol: false,
            lineStyle: {
                normal: {
                    width: 1
                }
            }
        }, {
            name: 'MA20',
            type: 'line',
            data: [],
            smooth: true,
            showSymbol: false,
            lineStyle: {
                normal: {
                    width: 1
                }
            }
        }, {
            name: 'MA30',
            type: 'line',
            data: [],
            smooth: true,
            showSymbol: false,
            lineStyle: {
                normal: {
                    width: 1
                }
            }
        }, {
            name: 'MA60',
            type: 'line',
            data: [],
            smooth: true,
            showSymbol: false,
            lineStyle: {
                normal: {
                    width: 1
                }
            }
        }]
    };
    window.myChart = echarts.init(document.getElementById('containers'));
    window.myChart.setOption(option)

    if(!window.kSocket) {
        // var data = [{"name":"2017-07-12 11:21:59","value":["2017-07-12 11:21:59","98.00","90.00","92.40","-8.16%",10]},{"name":"2017-07-12 11:21:59","value":["2017-07-12 11:21:59","98.00","90.00","92.40","-8.16%",10]},{"name":"2017-07-12 11:52:59","value":["2017-07-12 11:52:59","90.00","98.00","97.64","0.00%",110]},{"name":"2017-07-12 12:06:59","value":["2017-07-12 12:06:59","98.00","98.00","98.00","0.00%",11]}];
        window.kSocket = new WebSocket("wss://" + location.host + ":" + window.ws_port);

        window.kSocket.onopen = function (event) {
            window.kSocket.send("wstoken," + asset_type + ',' + type);
            window.timerId = setInterval(function () {
                // append(asset_type, type);
                if(window.kSocket){
                    window.kSocket.send("wstoken," + asset_type + ',' + type);
                }
            }, 1000)
        };

    }

    if(window.kSocket) {

        window.kSocket.onmessage = function (event) {
            var res = JSON.parse(event.data)
            if(res.code == '200') {
                var rawData = res.data
                var dates = rawData.map(function (item) {
                    return item[0];
                });

                window.barColorList = []

                var min = 0;
                var max = 0;

                var data = rawData.map(function (item) {
                    if (min == 0 || min >= parseFloat(item[5])) {
                        min = parseFloat(item[5]) * 0.9;
                    }
                    if (max == 0 || max < parseFloat(item[6])) {
                        max = parseFloat(item[6]) * 1.1;
                    }
                    window.barColorList[item[0]] = item[3] >= 0 ? redColor : greenColor
                    //       开        收       低        高       量
                    return [parseFloat(item[1]), parseFloat(item[2]), parseFloat(item[5]), parseFloat(item[6]), parseFloat(item[7]), item[0]];
                });

                // 柱状图
                var dataBar = rawData.map(function (item) {
                    return item[7];
                });

                option.series[0].data = dataBar
                option.series[1].data = data
                option.xAxis[0].data = dates
                option.xAxis[1].data = dates

                var dataMA10 = calculateMA(10, data);
                var dataMA20 = calculateMA(20, data);
                var dataMA30 = calculateMA(30, data);
                var dataMA60 = calculateMA(60, data);
                option.series[2].data = dataMA10
                option.series[3].data = dataMA20
                option.series[4].data = dataMA30
                option.series[5].data = dataMA60

                // option.yAxis[0].min = 'dataMin'
                // option.yAxis[0].max = 'dataMax'

                window.myChart.setOption(option);
            } else {
                console.log(res)
                window.kSocket.close();
                clearInterval(window.timerId)
            }
        }

    }

    if(window.kSocket && window.kSocket.readyState == 1){
        window.kSocket.send("wstoken," + asset_type + ',' + type);
    }

    if(window.kSocket && window.kSocket.readyState == 1 && window.timerId == 0) {
        window.timerId = setInterval(function () {
            // append(asset_type, type);
            if (window.kSocket && window.kSocket.readyState == 1) {
                window.kSocket.send("wstoken," + asset_type + ',' + type);
            }
        }, 1000)
    }

    // append(asset_type, type)

}

/**
 * 渲染分时图
 * @param asset_type
 * @param min
 * @param max
 */
function min_render(asset_type, min, max) {

    // if(window.kSocket) {
    //     window.kSocket.close();
    //     window.kSocket = null
    // }
    //
    // if(window.kSocket) {
    //     window.kSocket.close();
    //     window.kSocket = null
    // }

    var data = [ ];

    window.myChart = echarts.init(document.getElementById('containers'));

    option = {
        title: {
            text: ''
        },
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                var params = params[0];
                var res = params.seriesName + ' ' + params.name;
                return res += '<br />均价:' + params.value[3]+ '<br/> 涨跌：' + params.value[4] + '<br/> 成交：' + params.value[5];
            },
            axisPointer: {
                animation: false
            }
        },
        xAxis: {
            type: 'category',
            splitLine: {
                show: false
            },
            scale: true,
            data: []
        },
        yAxis: {
            type: 'value',
            boundaryGap: [0, '50%'],
            splitLine: {
                show: false
            },
            min: 'dataMin',
            max: 'dataMax',
        },
        series: [{
            name: '分时图',
            type: 'line',
            showSymbol: false,
            hoverAnimation: false,
            data: data
        }]
    };

    if(!window.kSocket) {
        window.kSocket = new WebSocket("wss://" + location.host + ":" + window.ws_port);
        // var data = [{"name":"2017-07-12 11:21:59","value":["2017-07-12 11:21:59","98.00","90.00","92.40","-8.16%",10]},{"name":"2017-07-12 11:21:59","value":["2017-07-12 11:21:59","98.00","90.00","92.40","-8.16%",10]},{"name":"2017-07-12 11:52:59","value":["2017-07-12 11:52:59","90.00","98.00","97.64","0.00%",110]},{"name":"2017-07-12 12:06:59","value":["2017-07-12 12:06:59","98.00","98.00","98.00","0.00%",11]}];
        window.kSocket.onopen = function (event) {
            window.kSocket.send("wstoken," + asset_type + ',1');
            // append(asset_type);
            window.timerId = setInterval(function () {
                if(window.kSocket) {
                    window.kSocket.send("wstoken," + asset_type + ',1');
                }
            }, 1000)
        };

        window.kSocket.onmessage = function (event) {
            var res = JSON.parse(event.data)
            if(res.code == 200) {
                var date = res.data.map(function(item) {
                    return item.name
                })
                option.series[0].data = res.data
                option.xAxis.data = date
                window.myChart.setOption(option);
            } else {
                console.log(res)
                window.kSocket.close();
                clearInterval(window.timerId)
            }
        }
    }

    if(window.kSocket && window.timerId == 0) {

        window.kSocket.onmessage = function (event) {
            var res = JSON.parse(event.data)
            if(res.code == 200) {
                var date = res.data.map(function(item) {
                    return item.name
                })
                option.series[0].data = res.data
                option.xAxis.data = date
                window.myChart.setOption(option);
            } else {
                console.log(res)
                window.kSocket.close();
                clearInterval(window.timerId)
            }
        }

        if(window.kSocket && window.kSocket.readyState == 1) {
            window.kSocket.send("wstoken," + asset_type + ',1');
        }

        window.timerId = setInterval(function () {
            if(window.kSocket && window.kSocket.readyState == 1) {
                window.kSocket.send("wstoken," + asset_type + ',1');
            }
        }, 1000)
    }

}
