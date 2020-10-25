@section('title', '新手指引')
@include("front.layouts.head")
<style>
  #guide{
    width: 100%;
    max-width: 643px;
    margin: 0 auto;
    padding-top: 20px;
    padding-bottom: 15px;
  }
  #guide h3{
    color: rgb(123, 12, 0);
  }
  #guide p {
    font-size: 14px;
    color: rgb(136, 136, 136);
  }
  #guide p span {
    font-size: 12px;
    color: rgb(136, 136, 136);
  }
</style>
<section id="newhand" class="">
  <div class="flowchart">
    <div class="ui container">
      <div class="chart itemsIntro">
          <img  class="ui centered medium image" src="/front/image/flowchart.png">
        <div id="guide">
          <h3>
            一、 关注益通云
          </h3>
          <p>
            1. 扫描以下二维码关注益通云：
          </p>
          <img style="width: 50%; max-width: 300px; clear: both;" src="/front/image/guide/qrcode_for_gh_da2e0c90c4f2_258.jpg">
          <h3>
            二、 注册
          </h3>
          <p>
            1. 进入益通云，点击右下方的“登录入口”：
          </p>
          <img src="/front/image/guide/1.png">
          <p>
            2. 点击“益通云”区域：
          </p>
          <img src="/front/image/guide/2.png">
          <p>
            3. 点击左下方的“立即注册”：
          </p>
          <img src="/front/image/guide/3.png">
          <p>
            4. 输入手机号，点击“获取验证码”：
          </p>
          <img src="/front/image/guide/4.png">
          <p>
            5. 填写手机收到的验证码，点击“立即注册”：
          </p>
          <img src="/front/image/guide/5.png">
          <p>
            6. 设定自己的登陆密码，需要输入两次进行确认，然后点击“确定”：
          </p>
          <img src="/front/image/guide/6.png">
          <h3>
            三、 登录
          </h3>
          <p>
            1. 输入手机号和登陆密码，点击“立即登陆”：
          </p>
          <img src="/front/image/guide/7.png">
          <p>
            2. 登录成功后出现益通云网站首页：
          </p>

          <img src="/front/image/guide/8.png">
          <h3>四、 设置交易密码</h3>
          <p>
            1. 进入“我的帐户”中的“用户管理中心”：
          </p>
          <img src="/front/image/guide/9.png">
          <p>2. 点击“帐户设置”：</p>
          <img src="/front/image/guide/10.png">
          <p>
            3. 向上滑动页面，可以看到“密码管理”区域，点击交易密码后面的“设置”：
          </p>
          <img src="/front/image/guide/11.png">
            <p>
              4. 点击“获取验证码”：
            </p>
          <img src="/front/image/guide/12.png">
          <p>
            5. 填写手机收到的验证码：
          </p>
          <img src="/front/image/guide/13.png">
          <p>
            6. 填写自己设定的交易密码，需要输入两次进行确认，然后点击“完成”：
          </p>
          <img src="/front/image/guide/14.png">
          <p>
            7. 出现“修改完成”的成功提示：
          </p>
          <img src="/front/image/guide/15.png">
          <h3>五、 充值</h3>
          <p>
            1. 点击网站右上角“我的帐户”，再点击“充值”：
          </p>
          <img src="/front/image/guide/16.png">
            <p>2. 出现充值中心页面：</p>
          <img src="/front/image/guide/17.png">
          <p>
            3. 向上滑动页面，填写充值金额，点击“支付”：
          </p>
          <img src="/front/image/guide/18.png">
          <p>4. 出现以下提示：</p>
          <img src="/front/image/guide/19.png">
          <p>5. 按照提示选择“在浏览器打开”：</p>
          <img src="/front/image/guide/20.png">
          <p>6. 在浏览器中打开的充值中心页面中再次填写充值金额，点击“支付”：</p>
          <img src="/front/image/guide/21.png">
          <p>7. 在支付宝中完成支付，会显示充值成功：</p>
          <img src="/front/image/guide/22.png">
          <p>8. 此时可以看到帐户中的资金余额：</p>
          <img src="/front/image/guide/23.png">
          <h3>六、 认购楼盘</h3>
          <p>1. 点击网站左上角的菜单按钮，选择“益通云认购中心”：</p>
          <img src="/front/image/guide/24.png">
          <p>2. 进入认购中心页面，点击需要认购的楼盘：
          </p>
          <img src="/front/image/guide/25.png">
          <p>3. 进入楼盘认购页面：</p>
          <img src="/front/image/guide/26.png">
          <p>4. 向上滑动页面，勾选协议，设定好认购数量，点击“确认认购”：
          </p>
          <img src="/front/image/guide/27.png">
          <p>5. 出现输入交易密码的提示：</p>
          <img src="/front/image/guide/28.png">
          <p>6. 输入交易密码，点击“确认认购”：</p>
          <img src="/front/image/guide/29.png">
          <p>7. 出现认购成功的提示：</p>
          <img src="/front/image/guide/30.png">
          <p>8. 在我的帐户中可以看到持有的楼盘数量，点击“查看详情”可以看到明细：</p>
          <img src="/front/image/guide/31.png">
          <p>9. 显示出帐户所持有的楼盘明细：</p>
          <img src="/front/image/guide/32.png">
          <h3>七、 交易楼盘</h3>
          <p>
            1. 进入楼盘交易中心
          </p>
          <p><span>点击网站左上角的菜单按钮，选择“交易中心”：</span></p>
          <img src="/front/image/guide/33.png">
            <p><span>出现楼盘的交易页面：</span></p>
          <img src="/front/image/guide/34.png">
          <p>2. 买入楼盘</p>
          <p><span>在楼盘的交易页面向上滑动到交易窗口，首先选择上方的“买入”选项，然后填写买入价格、买入数量、交易密码（进入交易中心后的第一次交易操作需要输入，之后半个小时内不需要重复输入），再点击下方的“买入”按钮：</span></p>
          <img src="/front/image/guide/35.png">
          <p><span>出现买入确认窗口，点击“确认”按钮：</span></p>
          <img src="/front/image/guide/36.png">
          <p><span>弹出买入下单成功的提示：</span></p>
          <img src="/front/image/guide/37.png">
          <p><span>交易密码有半小时的记忆功能，第一次交易操作后，接下来的半小时内，交易密码输入框不会再出现：</span></p>
          <img src="/front/image/guide/38.png">
          <p>3. 卖出楼盘</p>
          <p><span>选择上方的“卖出”选项，然后填写卖出价格、卖出数量、再点击下方的“卖出”按钮：</span></p>
          <img src="/front/image/guide/39.png">
          <p><span>出现卖出确认窗口，点击“确认”按钮：</span></p>
          <img src="/front/image/guide/40.png">
          <p><span>弹出卖出下单成功的提示：</span></p>
          <img src="/front/image/guide/41.png">
            <p>4. 撤销委托</p>
          <p><span>在楼盘的交易页面向上滑动到“我的委托”窗口，可以看到未成交的交易委托，如果我们不想继续等待成交，可以手动撤消。比如当前《心经》楼盘的行情价格为153.10，我们在152.00qcash价位委托买入5幅：</span></p>
          <img src="/front/image/guide/42.png">
          <img src="/front/image/guide/43.png">
          <img src="/front/image/guide/44.png">
          <p><span>由于我们下单后，《心经》的行情价格没有回到152qcash价位，所以这笔交易委托一直无法成交，我们可以在“我的委托”里面撤消这笔交易委托，点击其下面的“撤消”按钮可以完成撤消：</span></p>
          <img src="/front/image/guide/45.png">
          <img src="/front/image/guide/46.png">
          <img src="/front/image/guide/47.png">
        </divs>


      </div>
      <div class="ui middle aligned grid">
        <div class="row">
          <div class="center aligned column">
              <a href="/subscription"><img class="go_buy" src="/front/image/go_buy.png"></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@include("front.layouts.foot")
