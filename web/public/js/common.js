/**
 * Created by alan on 17-3-4.
 */
function lv_delete(obj)
{
    var url = $(obj).data('url');
    swal({
            title: "确定删除吗?",
            text: "删除后将不可恢复",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "是",
            cancelButtonText: "否",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },
        function(){
            $.ajax({
                url:url,
                type:'get',
                dataType:'json',
                success: function (res) {
                    if(res.code != 200) {
                        show_message('删除失败', '请检查您的网络参数', 'error');
                    } else {
                        show_message('删除成功', '你已经成功删除,不可恢复', 'success');
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    }
                },
                error: function () {
                    show_message('删除失败', '请检查您的网络参数', 'error');
                }
            });

        });
}
function lv_change(obj){
    var url = $(obj).data('url');
    swal({
            title: "确定将该栏目设置为该状态吗?",
            text: "切换后将在前台动态改变状态,这将影响前后台状态显示",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "是",
            cancelButtonText: "否",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        },
        function(){
            $.ajax({
                url:url,
                type:'post',
                dataType:'json',
                success: function (res) {
                    if(res.code != 200) {
                        show_message('设置失败', '请检查您的网络参数', 'error');
                    } else {
                        show_message('设置成功', '你已经设置成功', 'success');
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    }
                },
                error: function () {
                    show_message('设置失败', '请检查您的网络参数', 'error');
                }
            });

        });
}

function is_show(obj){
    var url = $(obj).data('url');
    $.ajax({
        url:url,
        type:'post',
        dataType:'json',
        success: function () {
            // if(res.code != 200) {
            //     show_message('设置失败', '请检查您的网络参数', 'error');
            // } else {
            //     show_message('设置成功', '你已经设置成功', 'success');
            //     setTimeout(function () {
            //         window.location.reload();
            //     }, 500);
            // }
            window.location.reload();
        },
        error: function () {
            show_message('设置失败', '请检查您的网络参数', 'error');
            window.location.reload();
        }
    });
}

function lv_message($msg)
{
    swal($msg);
}
function show_message($msg, $cont, $type){
    swal($msg, $cont, ($type?$type:'success'))
}
function check_money(money) {
    var result = true;
    var string_price = money.toString().split(".");
    if(string_price.length > 1) {
        if (string_price.length <= 2) {
            if (string_price[1].length <= 2) {
                value.toString();
            } else {
                result = false;
            }
        } else {
            result = false;
        }
    }
    if(!string_price[0] && !string_price[1]) {
        result = false;
    }
    if(!/(^[1-9]\d*(\.\d{1,2})?$)|(\.)|(^0(\.\d{1,2})?$)/.test(that.val())){
        result = false;
    }
    return result;
}