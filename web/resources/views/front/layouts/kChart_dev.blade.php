<div class="container-fluid" style="background: #fff;padding: 10px">
    <div class="row-fluid example">
        <script>
            var data = '{!! json_encode($k_chart) !!}';
            var date = '{!! json_encode($k_chart['date']) !!}';
            var k_date = JSON.parse(date);
            var data = '{!! json_encode($k_chart['data']) !!}';
            var k_data = eval('('+data+')');
            var params = '{!! json_encode($k_chart['params']) !!}';
            var k_params = JSON.parse(params);
            var _params = '{!! json_encode($k_chart['_params']) !!}';
            var _k_params = JSON.parse(_params);
        </script>
        <div id="sidebar-code" class="col-md-4" style="display: none;">
            <div class="well sidebar-nav" >
                <div class="nav-header">
                    <a href="#" onclick="autoResize()" class="glyphicon glyphicon-resize-full" id ="icon-resize" ></a>option
                </div>
                <textarea id="code" name="code">
option = {
    title : {
        text: '交易平台'
    },
    tooltip : {
        trigger: 'axis',
        formatter: function (params) {
                    console.log(_k_params);
                    console.log(_k_params[params[0][1]]);
             var res = params[0].seriesName + ' ' + params[0].name;
                    res += '<br/>  开盘 : ' + params[0].value[0] + '  最高 : ' + params[0].value[3];
            res += '<br/>  收盘 : ' + params[0].value[1] + '  最低 : ' + params[0].value[2];
            res += '<br/>  成交额 : ' + _k_params[params[0][1]][0] + '  成交量 : ' + _k_params[params[0][1]][1];
            return res;
        }
    },
    legend: {
        data:['交易曲线']
    },
    toolbox: {
        show : false,
        feature : {
            mark : {show: true},
            dataZoom : {show: true},
            dataView : {show: true, readOnly: false},
            magicType: {show: true, type: ['line', 'bar']},
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    dataZoom : {
        show : true,
        realtime: true,
        start : 10,
        end : 100
    },
    xAxis : [
        {
            type : 'category',
            boundaryGap : true,
            axisTick: {onGap:false},
            splitLine: {show:true},
            data : k_date,
        },

    ],
    yAxis : [
        {
            type : 'value',
            scale:true,
            'name':'价格 (RMB)',
        }
    ],
    series : [
        {
            name:'交易曲线',
            type:'k',
            data:k_params
        }
    ]
};
                    </textarea>
            </div><!--/.well -->
        </div><!--/span-->
        <div id="graphic" class="col-md-12">
            <div id="main" class="main"></div>
            <div>
            {{--<button type="button" class="btn btn-sm btn-success" onclick="refresh(true)">刷 新</button>--}}
            <!--                     <span class="text-primary">切换主题</span>
                 -->                    <select id="theme-select" style="display: none"></select>

                <span id='wrong-message' style="color:red"></span>
            </div>
        </div><!--/span -->
    </div><!--/row-->

</div>