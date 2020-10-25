@if ( env('APP_DEBUG') === true )
    {{--<script src="//localhost:35729/livereload.js?snipver=1" type="text/javascript"></script>--}}
    <script>
        window.yidebug = true
    </script>
@endif
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