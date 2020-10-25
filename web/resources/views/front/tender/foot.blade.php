@if(Auth::guard('front')->user())
    <script>
        // 获取未读消息
        $.getJSON('/tender/unReadMsgCount', {}, function (res) {
            if(res.code == 200 && res.data > 0){
                $('.unread-msg-count').text(res.data)
            }
        })
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
