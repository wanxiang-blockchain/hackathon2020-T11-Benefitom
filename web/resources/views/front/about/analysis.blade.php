@section('title', '行情分析')
@include("front.layouts.head")
<div class="aboutContainer pusher">
  <div class="ui container">
    <div class="pusher">
      <div class="aboutChange">
          <div class="ui stackable inverted equal height stackable grid">
            @include("front.layouts.aboutLeftTree")
            <div class="thirteen wide column aboutInfo">
          <div class="companyDynamic aboutCommonHeader">
            <h3 class="ui header">
              <i class="talk icon"></i>
              <div class="content">行情分析</div>
            </h3>
            <div class="aboutList">
              @if(empty($data['0']))
                <div class="ui middle aligned center aligned grid">
                  <div class="column">暂无内容</div>
                </div>
              @else
                <div class="ui middle aligned list">
                  @foreach($data as $value)
                    <a class="item" href="{{route('about/analysis',['id'=>$value->id])}}">
                      <div class="right floated content">{{$value->updated_at}}</div>
                      <div class="content">{{$value->title}}</div>
                    </a>
                  @endforeach
                </div>
              @endif
            </div>
            {{$data->links()}}
            </div>
            {{--<div class="center aligned column">--}}
              {{--<div class="ui pagination menu">--}}
                {{--<a class="item" href="#">上一页</a>--}}
                {{--<a class="item active" href="#">1</a>--}}
                {{--<a class="item" href="#">2</a>--}}
                {{--<a class="item" href="#">下一页</a>--}}
              {{--</div>--}}
            {{--</div>--}}
            <!-- 没内容 -->
            <!-- <div class="ui middle aligned center aligned grid">
              <div class="column">暂无内容</div>
            </div> -->
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@include("front.layouts.foot")
