@include('front.tender.head')
    <link rel="stylesheet" href="/tender/css/product-details.css">
</head>
<body ontouchstart>

<div class="wrap">
    <div class="main">
        <div style="width:100%;">
            <video width="100%" src="{{$model->video}}" controls poster="{{$model['poster']}}">
                Sorry, your browser doesn't support embedded videos,
                but don't worry, you can <a href="videofile.webm">download it</a>
                and watch it with your favorite video player!
            </video>
        </div>

        <div class="section2">
            <div class="details">
                <span class="active">图文详情</span>
                {{--<span>商品参数</span>--}}
            </div>
            <!-- 详情图 -->
            <div class="detailsimg">
                {!! $model->info !!}
            </div>
        </div>
    </div>

</div>
<script src="/tender/js/jquery.min.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="/js/google-analysis.js"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-46679934-7');
</script>
</body>
</html>
