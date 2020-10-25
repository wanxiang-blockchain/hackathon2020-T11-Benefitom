@section('title',$data->title)
@include("front.layouts.head")
<style>
    .dynamic_div{
        overflow: hidden;
        padding-top: 20px;
    }
    .dynamic_div a {
        color: #666666;
        font-size: 13px;
        width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .dynamic_div a:hover{
        color: #cba078;
    }
    .fl{
        float: left;
    }
    .fr{
        float: right;
        text-align: right;
    }
    @media only screen and (min-width: 1200px){
        .dynamic_div{position: absolute;bottom: 30px;width: 86%;}
        .dynamic_div a{width: 370px!important;}
        .fr{text-align: right;}
    }


</style>
<div class="aboutContainer pusher">
    <div class="ui container">
        <div class="pusher">
            <div class="aboutChange">
                <div class="ui stackable inverted equal height stackable grid">
                    @include("front.layouts.aboutLeftTree")
                    <div class="thirteen wide column aboutDetailsInfo">
                        <h3>{{$data->title}}</h3>
                        {!! $data->content!!}
                        <div class="dynamic_div">
                            @if(\Cache::get("{$data->id}_pre", false) != false)
                                <a class="dynamic_div_a1 fl"
                                   href='{{basename(request()->path())}}?id={{\Cache::get("{$data->id}_pre")}}' title="{{\Cache::get("{$data->id}_pt")}}">上一篇 : {{\Cache::get("{$data->id}_pt")}}</a>
                            @endif
                            @if(\Cache::get("{$data->id}_next", false) != false)
                                <a class="dynamic_div_a2 fr"
                                   href='{{basename(request()->path())}}?id={{\Cache::get("{$data->id}_next")}}'  title="{{\Cache::get("{$data->id}_nt")}}">下一篇 : {{\Cache::get("{$data->id}_nt")}}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include("front.layouts.foot")
