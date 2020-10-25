<script src="http://www.transfer.com/front/js/jquery.min.js"></script>
<form action="https://my-uat1.orangebank.com.cn/khpayment/UnionAPI_Open.do" method="POST">
    <input type="hidden" name="sign" value="{{$sign}}" />
    <input type="hidden" name="orig" value="{{$orig}}" />
    <input type="hidden" name="returnurl" value="{{$returnurl}}" />
    <input type="hidden" name="NOTIFYURL" value="{{$notice}}" />
    <input type="submit">
</form>
<script>
    $('form').submit();
</script>

