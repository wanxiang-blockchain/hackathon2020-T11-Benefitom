/**
 * Created by johnShaw on 17/7/13.
 */

function getWithLock(url, option, _success, lock) {
    //  请求锁定
    if (sessionStorage.getItem(lock) == 1)
        return false;
    sessionStorage.setItem(lock, 1)
    $.get(url, option, function (res) {
        sessionStorage.removeItem(lock)
        _success(res)
    })
}

