@include('front.tender.head')
    <link rel="stylesheet" href="css/auction-contract.css">
    <style>
        .main{
            padding-top: 5rem;
        }
        textarea{
            border: 1px solid #000000;
            width: 100%;
            height: 100px;
            margin-top: 2rem;
        }
        button.agree {
            display: block;
            width: 100%;
            background: #cc0000;
            color: #fffffc;
            margin-top: 15px;
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
        }
    </style>
</head>
<body >
<div class="wrap">
    <div class="main">
        <p>有什么意见或建议请告诉我们：</p>
        <textarea id="con"></textarea>
        <button class="agree">提 交</button>
    </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="/tender/js/jquery-weui.min.js"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : "{{ csrf_token() }}"
            }
        });
        $('.agree').on('click', function () {
            var con = $('#con').val();
            if (con == '') {
                $.alert('请输入您的反馈')
                return false;
            }
            $('#con').val('')
            $.showLoading()
            $.ajax('/tender/feedback', {
                data: {con: con},
                dataType: 'json',
                method: 'post',
                success: function(res){
                    $.hideLoading()
                    $.alert("感谢您的反馈，你的建议是我们进步的动力！")
                },error: function () {
                    $.hideLoading()
                    $.alert("感谢您的反馈，你的建议是我们进步的动力！")
                }
            })
        })
    })
</script>
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
