
<div class="ui page dimmer">
    <div class="content">
        <div class="center"></div>
    </div>
</div>
<script type="text/javascript" src="/front/js/jquery.min.js"></script>
<script type="text/javascript" src="/front/js/semantic.min.js"></script>
<script type="text/javascript" src="/front/js/WdatePicker.js"></script>
<script type="text/javascript" src="/front/js/jquery.validate.js"></script>
<script type="text/javascript" src="/front/js/videojs-ie8.min.js"></script>
<script type="text/javascript" src="/front/js/messages_cn.js"></script>
<script type="text/javascript" src="/front/js/app.js?v=1"></script>
<script type="text/javascript" src="/front/js/common.js?v=9"></script>
 {{-- <script type="text/javascript" src="/front/js/form.js?v=4"></script>--}}
<script type="text/javascript" src="/js/admin/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/front/js/jquery-getui.js"></script>
<script type="text/javascript" src="/js/jsencrypt.min.js"></script>
@stack('endscripts')
<script>
    function isInteger(obj) {
        return Math.floor(obj) === obj
    }
  $(function () {
      $('input[type="number"],input[type="tel"]').on('keyup', function () {
          var val = parseInt($(this).val());
          if(!isInteger(val)) {
              $(this).val("");
          }
      });
  })
</script>
<script>
    $(function () {
        $('.cert').on('click', function () {
            $('.dimmer').dimmer('show');
            $('meta[name=viewport]').prop('content', 'width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=2.0,user-scalable=yes');
            var img = $(this).prop('src')
            $('.dimmer .content .center').html('<img style="width: 100%; max-width: 600px;" src="' + img + '" />')
            $('.dimmer img').on('click', function () {
                $('.dimmer').dimmer('hide');
            })
        })
        $('.dimmer').on('click', function () {
            $('.dimmer').dimmer('hide');
            $('meta[name=viewport]').prop('content', 'width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no');
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
