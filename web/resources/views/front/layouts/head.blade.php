<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name='viewport', content='width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no' />
  <meta http-equiv='X-UA-Compatible', content='ie=edge' />
  <link rel="shortcut icon" href="{{'/front/favicon.ico?v=1'}}">
  <link rel="stylesheet" type="text/css" href="/front/css/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="/front/css/video-js.css">
  <link rel="stylesheet" type="text/css" href="/front/css/common.css?v=13">
  <link rel="stylesheet" type="text/css" href="/front/css/user.css?v=3">
  <link rel="stylesheet" type="text/css" href="/front/css/index.css?v=23">
  <link rel="stylesheet" type='text/css' href="/front/css/newh.css?v=4">
  <link rel="stylesheet" type='text/css' href="/js/admin/plugins/sweetalert/sweetalert.css">
</head>
<body id="tangsheng" class="pushable index">
<!-- Sidebar Menu -->
<div class="ui vertical inverted sidebar menu left go_reg">
  <a class="@if(Request::path() == 'subscription') active item @else item @endif"  href="{{route('subscription')}}">益通云</a>

</div>
<div class="ui fixed inverted main menu go_reg">
  <div class="ui container">
    <a class="launch icon item">
      <i class="content icon"></i>
    </a>
    <div class="right menu">
      <div class="item">
        @if(\Auth::guard('front')->check())
          <div class="ui primary button dropdown">
            我的账户
            <div class="menu">
              <a class="item" href="{{route('member/index')}}">用户管理中心</a>
              <a class="item" href="{{route('member/recharge')}}">充值</a>
              <a class="item" href="{{route('member/withdraw')}}">提现</a>
              <a class="item" href="{{route('member/deliveries')}}">提货</a>
              @if(\Auth::guard('front')->check())
                {{--<a class="item" href="javascript:;">{{\Auth::guard('front')->user()->phone}}</a>--}}
                <a class="item" href="{{route('logout')}}">退出</a>
              @else

              @endif
            </div>
          </div>
        @else
          <a class="go_reg" href="/login">登录</a>|
        @endif

      </div>
    </div>
  </div>
</div>
<div class="following bar">
  <div class="ui inverted main menu topbar">
    <div class="ui container">
      <div class="left menu">
        <div class="item" style="padding: 0;">链英科技出品 </div>
      </div>
      <div class="right menu">
        <div class="item">
          @if(\Auth::guard('front')->check())
            {{\Auth::guard('front')->user()->phone}} 您好!
            {{--<a class="ui" href="{{route('logout')}}">退出</a>--}}
          @else
            <a class="ui go_reg" href="/login">登录</a>
          @endif
        </div>
        {{--<div class="item" style="float: left;">关注我们</div>--}}
      </div>
    </div>
  </div>
  <div class="ui inverted vertical masthead center aligned segment go_reg">
    <div class="ui container">
      <div class="ui large secondary inverted netword menu pointing">
        <div class="item header">
          <div class="ui logo">
            <div class="sides">
              <a href="/" style="color: red;" class="active ui side">
                <img style="width:200px;" src="/front/image/logo.png" />
              </a>
            </div>
          </div>
        </div>

        <a class="@if(strrpos(Request::path(),'subscription')!==false) active item @else item @endif"  href="{{route('subscription')}}">益通云</a>
        @if (\Auth::guard("front")->user())
          <a href="#" class="ui right floated dropdown item">
            我的账户
            <div class="menu">
              <div class="item" onclick="location.href='{{route('member/index')}}'">用户管理中心</div>
              <div class="item" onclick="location.href='{{route('member/recharge')}}'">充值</div>
              <div class="item" onclick="location.href='{{route('member/withdraw')}}'">提现</div>
              <div class="item" onclick="location.href='{{route('member/deliveries')}}'">提货</div>
              @if(\Auth::guard('front')->check())
                <div class="item" onclick="location.href='{{route('logout')}}'">退出</div>
              @endif
            </div>
          </a>
        @endif
      </div>
    </div>
  </div>
</div>


