@section('title', '加入我们')
@include("front.layouts.head")
<div class="aboutContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="aboutChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include("front.layouts.aboutLeftTree")
                    <div class="thirteen wide column aboutInfo">
                        <div class="joinUs aboutCommonHeader">
                            <h3 class="ui header">
                                <img class="ui image" src="/front/image/about/icon4.png">
                                <div class="content">招贤纳士</div>
                            </h3>
                            <div class="emailAddress">
                              <h3>绍德艺品易货中心拟在全国各地陆续投资建立艺术馆，长期招募专职/兼职工作人员
                              </h3>
                            </div>
                            <h4>招募职位：</h4>
                            <p>
                               （1）馆长
                            </p>
                            <p>（2）中心主任</p>
                            <p>（3）艺术总监</p>
                            <p>（4）投资顾问</p>
                            <p>（5）艺术客服</p>
                            <h4>申请条件：符合如下条件之一者皆可申请</h4>
                            <p>
                                （1）具有艺术教育背景、或艺术从业经历者
                            </p>
                            <p>
                                （2）具有金融、广告、营销、IT、师范、等教育背景、或从业经历者
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
