
@if(count($errors) > 0)
    <div id="error" data-json='{!! $error_notice = json_encode($errors->all()) !!}'></div>
@else
    <div id="error" data-json=""></div>
@endif

<script>
    var error = $('#error').data('json');
    if (error.length > 0) {
        for (var i = 0; i < error.length; i++) {
            noty({
                text: error[i], layout: 'topRight', type: 'error', animation: {
                    open: {height: 'toggle'},
                    close: {height: 'toggle'},
                    easing: 'swing',
                    speed: 500
                }
            });
        }
    }
</script>

