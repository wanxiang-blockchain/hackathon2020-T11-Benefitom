<div class="three wide column leftTree">
  <div class="ui vertical inverted sticky menu">
    <div class="item">
      <div class="header">用户管理中心＞
        @if(request()->path() =='member')
          总览
          @elseif(request()->path() == 'member/recharge' || request()->path() =='member/withdraw')
          充值中心
          @elseif(request()->path() == 'member/subscription')
          我的认购
          @elseif(request()->path() == 'member/flow')
          资金流水
          @elseif(in_array(request()->path(), ['member/setting', 'member/oneChangePhone', 'member/twoChangePhone']))
          账户设置
        @endif
      </div>
      <div class="menu">
        <a class="item @if(request()->path() == 'member')active @endif" href="{{route('member/index')}}">
          <i class="yen icon"></i>
          我的资产
        </a>
        <a class="item @if(request()->path() == 'member/deliveries')active @endif" href="{{route('member/deliveries')}}">
          <i class="yen icon"></i>
          订货通览
        </a>
{{--        @if(\App\Service\MemberService::isAgent())--}}
        <a class="item @if(request()->path() == 'member/invite')active @endif" href="{{route('member/invite')}}">
          <i class="yen icon"></i>
          我的推广
        </a>
        {{--@endif--}}
        <a class="item @if(in_array(request()->path(), ['member/userinfo', 'member/userinfoEdit', 'member/setting', 'member/oneChangePhone', 'member/twoChangePhone','member/resetTradePassword', 'member/resetTradePasswordSuccess'])) active @endif" href="{{route('member/setting')}}">
          <i class="setting icon"></i>
          我的账户
        </a>
      </div>
    </div>
  </div>
</div>
