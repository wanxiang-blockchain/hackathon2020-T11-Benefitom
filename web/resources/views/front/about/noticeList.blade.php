@section('title', '官方公告')
@include("front.layouts.head")
<div class="aboutContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="aboutChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include("front.layouts.aboutLeftTree")
                    <div class="thirteen wide column aboutInfo">
                        <div class="aboutNotice aboutCommonHeader">
                            <h3 class="ui header">
                                <i class="volume up icon"></i>
                                <div class="content">官方公告</div>
                            </h3>
                            <div class="aboutList">
                                @if(empty($data['0']))
                                    <div class="ui middle aligned center aligned grid">
                                        <div class="column">暂无内容</div>
                                    </div>
                                @else
                                    <div class="ui middle aligned list">
                                        @foreach($data as $value)
                                            <a class="item" href="{{route('about/notice',['id'=>$value->id])}}">
                                                <div class="right floated content">{{$value->updated_at}}</div>
                                                <div class="content">{{$value->title}}</div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("front.layouts.foot")
