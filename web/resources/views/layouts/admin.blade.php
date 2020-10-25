    <!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <!-- META SECTION -->
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="shortcut icon" href="{{'/front/favicon.ico'}}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{--<link rel="icon" href="favicon.ico" type="image/x-icon" />--}}
    <link rel="shortcut icon" href="{{'/front/favicon.ico'}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- END META SECTION -->
    <!-- CSS INCLUDE -->
    @include('UEditor::head')
    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('css/admin')}}/theme-default.css?v=2018"/>
    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('js/admin')}}/plugins/sweetalert/sweetalert.css"/>
    <script type="text/javascript" src="{{asset('js')}}/jsencrypt.min.js"></script>
    <script type="text/javascript" src="{{asset('js/admin')}}/plugins/jquery/jquery.min.js"></script>
@yield('customer_css')
    <!-- EOF CSS INCLUDE -->
</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container">
    <!-- START PAGE SIDEBAR -->
    <div class="page-sidebar">
        <!-- START X-NAVIGATION -->
        <ul class="x-navigation">
            <li class="xn-logo">
                <a href="#">管理员</a>
                <a href="#" class="x-navigation-control"></a>
            </li>
            @foreach($menu as $k => $value)
            <li class="{{$value['class']}}">
                <a href="@if(!$value['subMenu']){{$value['url']}}@else#@endif"><span class="{{$value['icon']}}"></span> <span class="xn-text">{{$value['name']}}</span></a>
                @if($value['subMenu'])
                    <ul>
                    @foreach($value['subMenu'] as $_value)
                            <li class="{{$_value['class']}}"><a href="{{$_value['url']}}"><span class="{{$_value['icon']}}"></span> {{$_value['name']}}</a></li>
                    @endforeach
                    </ul>
                @endif
            </li>
            @endforeach
        </ul>
        <!-- END X-NAVIGATION -->
    </div>
    <!-- END PAGE SIDEBAR -->

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- START X-NAVIGATION VERTICAL -->
        <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
            <!-- TOGGLE NAVIGATION -->
            <li class="xn-icon-button">
                <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
            </li>
            <!-- END TOGGLE NAVIGATION -->
            <!-- SIGN OUT -->
            {{--<li class="xn-icon-button " style="margin-left: 75%">
                <a style="width: 100%">您好，{{\Auth::user()->phone}}</a>
            </li>--}}
            <li class="pull-right">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">您好,{{\Auth::user()->phone}}<span class="fa fa-sign-out"></span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="/admin/manage/edit?id={{\Auth::user()->id}}">个人信息</a>
                    </li>
                    <li>
                        <a href="#" class="mb-control" data-box="#mb-signout">退出</a>
                    </li>
                </ul>

            </li>
            <!-- END SIGN OUT -->
        </ul>
        <!-- END X-NAVIGATION VERTICAL -->

        <!-- START BREADCRUMB -->
        <ul class="breadcrumb">
            {!!$crumbs !!}
        </ul>
        <!-- END BREADCRUMB -->

        <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
            @yield('content')
            <!-- END DASHBOARD CHART -->

        </div>
        <!-- END PAGE CONTENT WRAPPER -->
    </div>
    <!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->

<!-- MESSAGE BOX-->
<div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title"><span class="fa fa-sign-out"></span> 退出 <strong>登录</strong> ?</div>
            <div class="mb-content">
                <p>你确定退出登录吗?</p>
                <p>如果要继续工作，请按否。 按是注销当前用户。</p>
            </div>
            <div class="mb-footer">
                <div class="pull-right">
                    <a href="{{asset('admin/logout')}}" class="btn btn-success btn-lg">是</a>
                    <button class="btn btn-default btn-lg mb-control-close">否</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MESSAGE BOX-->
<!-- END PRELOADS -->
<audio id="audio-alert" src="{{asset('assets/audio')}}/alert.mp3" preload="auto"></audio>
<audio id="audio-fail" src="{{asset('assets/audio')}}/fail.mp3" preload="auto"></audio>
<!-- START SCRIPTS -->
<!-- START PLUGINS -->

<script type="text/javascript" src="{{asset('js/admin')}}/plugins/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap.min.js"></script>
<!-- END PLUGINS -->
<script type='text/javascript' src='{{asset('js/admin')}}/plugins/noty/jquery.noty.js'></script>
<script type='text/javascript' src='{{asset('js/admin')}}/plugins/noty/layouts/topRight.js?v=2'></script>
<script type='text/javascript' src='{{asset('js/admin')}}/plugins/noty/themes/default.js'></script>
<!-- START THIS PAGE PLUGINS-->
<script type='text/javascript' src='{{asset('js/admin')}}/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/scrolltotop/scrolltopcontrol.js"></script>
``
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/morris/raphael-min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/morris/morris.min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/rickshaw/d3.v3.js"></script>
<script type='text/javascript' src='{{asset('js/admin')}}/plugins/bootstrap/bootstrap-datepicker.js'></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/owl/owl.carousel.min.js"></script>

<script type="text/javascript" src="{{asset('js/admin')}}/plugins/moment.min.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/bootstrap/bootstrap-select.js"></script>

<!-- END THIS PAGE PLUGINS-->
<!-- START TEMPLATE -->
<script type="text/javascript" src="{{asset('js/admin')}}/settings.js?v=1"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/actions.js"></script>
<script type="text/javascript" src="{{asset('js/admin')}}/plugins/sweetalert/sweetalert.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.5.2/js/fileinput.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.5.2/js/locales/zh.js"></script>
<script type="text/javascript" src="{{asset('js')}}/common.js?v=1"></script>
<script type="text/javascript" src="/front/js/WdatePicker.js?v=4"></script>
@stack('scripts')
<!-- END TEMPLATE -->
<!-- END SCRIPTS -->
<script>
    $().ready(function() {
        $('.kv-file-remove').on('click', function(){
            $(this).parent().parent().parent().parent().parent().remove();
            var length = $('.show_thumb').find('.file-preview-thumbnails').length;
            if(length<=0) {
                $('.show_thumb_box').remove();
            }
        });
    });
    (function($){
        "use strict";
        $(".file")
                .fileinput({
                    language: 'zh',
                    /*uploadUrl: "/Product/imgDeal",
                     autoReplace: true,
                     allowedFileExtensions: ["jpg", "png", "gif"],
                     browseClass: "btn btn-primary", //按钮样式
                     previewFileIcon: "<i class='glyphicon glyphicon-king'></i>"*/
                })
                .on("fileuploaded", function (e, data) {
                    var res = data.response;
                    if (res.state > 0) {
                        alert('上传成功');
                        alert(res.path);
                    }
                    else {
                        alert('上传失败')
                    }
                })
    })(window.jQuery);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@include('layouts.error')
</body>
</html>






