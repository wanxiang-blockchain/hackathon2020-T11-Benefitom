@section('title', '个人信息编辑 - 用户管理中心')
@include("front.layouts.head")
<div class="userContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="userChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include('front.layouts.leftTree')
                        <div class="userMain">
                            @if(empty($member->name))
                            <p>请进行实名认证，带 <i class="asterisk icon"></i>字段为必填字段</p>
                            @endif
                            <form class="ui form" id="form" action="/member/userinfoEdit" method="post">
                                {{csrf_field()}}
                                @if(!empty($validator))
                                    <p style="color: red;"><b>Erro: {{$validator['data']}}</b></p>
                                @endif
                                <div class="field">
                                    <input name="name" value="{{$member->name}}" id="name" type="text" placeholder="请输入姓名" required>
                                    <div class="ui corner label">
                                        <i class="asterisk icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <input name="idno" value="{{$member->idno}}"  id="idno" type="text" placeholder="请输入身份证号" required>
                                    <div class="ui corner label">
                                        <i class="asterisk icon"></i>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="inline fields">
                                        <label>性别</label>
                                        <div class="field">
                                            <div class="ui radio ">
                                                <input type="radio" name="sex" value="1" @if($member->sex == 1) checked="checked" @endif>
                                                <label>男</label>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="ui radio ">
                                                <input type="radio" name="sex" value="2"@if($member->sex == 2) checked="checked" @endif>
                                                <label>女</label>
                                            </div>
                                        </div>
                                        <div class="ui corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="field">
                                    <input name="sec_phone" id="sec_phone" type="text" placeholder="请输入备用电话">
                                </div>
                                <div class="field">
                                    <p>为保证您能正常使用本产品所有功能，请正确填写实名信息。</p>
                                </div>
                                <input id="submit" type="submit" class="ui orange submit button right formBtn" value='确定'>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
