<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name='viewport', content='width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no' />
    <meta http-equiv='X-UA-Compatible', content='ie=edge' />
    <link rel="shortcut icon" href="{{'/front/favicon.ico?v=1'}}">
    <link rel="stylesheet" type="text/css" href="/front/css/semantic.min.css">
    <link rel="stylesheet" type='text/css' href="/rong/css/rong.css?v=11">
    <link rel="stylesheet" type='text/css' href="/js/admin/plugins/sweetalert/sweetalert.css">
    <script src="/front/js/jquery.min.js" type="application/javascript"></script>
    <script type="text/javascript" src="/js/admin/plugins/sweetalert/sweetalert.min.js"></script>
</head>
<body id="rong">
<div id="banner" class="rong">
    <img src="/rong/image/rong_logo.png">
</div>
<header class="head-nav">
    <nav>
        <ul class="rong">
            <li><a class="active" href="/rong" ><img src="/rong/image/nav_1.jpg?"></a></li>
            <li><a class="active" href="/" ><img src="/rong/image/nav_2.jpg"></a></li>
            <li><a class="active" href="{{\App\Utils\UrlUtil::flextHuiUrl()}}" ><img src="/rong/image/nav_3.jpg"></a></li>
            <li><a class="active" href="/#" ><img src="/rong/image/nav_4.jpg"></a></li>
        </ul>
    </nav>
</header>
<div id="mobileMenu" class="ui four item menu rong">
    <a class="item active" href="/rong">艺融宝</a>
    <a class="item" href="/">益通云</a>
    <a class="item" href="{{\App\Utils\UrlUtil::flextHuiUrl()}}">易宝堂</a>
    <a class="item" href="#">艺奖堂</a>
</div>
<div id="mobileNavPad"></div>