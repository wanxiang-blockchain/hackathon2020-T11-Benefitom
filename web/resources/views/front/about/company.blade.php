@section('title', '关于我们')
@include("front.layouts.head")
<div class="aboutContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="aboutChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include("front.layouts.aboutLeftTree")
                    <div class="thirteen wide column  aboutInfo">
                        <div class="aboutCompany">
                            <h3></h3>
                            <h3>益通云介绍</h3>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include("front.layouts.foot")
