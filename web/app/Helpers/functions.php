<?php
function order_id($prefix = "", $postfix = '')
{
    list($a, $b) = explode(" ", microtime());
    $a = substr($a, 2);
    $l = 10 - strlen($prefix) - strlen($postfix);
    return strtoupper($prefix . date("YmdHis") . $a . $postfix . str_random($l));
}

//判断是否是微信
function is_weixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'],
            'MicroMessenger') !== false
    ) {
        return true;
    }
    return false;
}

//生成唯一不重复的32位订单号
function order_no()
{
    $pix = substr(str_replace('.', '', uniqid('', true)), 0, -5);
    $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $order_sn = $year_code[intval(date('Y')) - 2017] .
        strtoupper(dechex(date('m'))) . date('d') .
        substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('d', rand(0, 99));
    return $pix . $order_sn;
}

function wx_order_no()
{
	return '*' . uniqid() . randStr(18);
}

function randStr($len = 6, $format = 'ALL')
{
    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnopqrstuvwxyz23456789';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
            break;
        case 'NUMBER':
            $chars = '123456789';
            break;
        default :
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            break;
    }
    mt_srand((double)microtime() * 1000000 * getmypid());
    $password = "";
    while (strlen($password) < $len)
        $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
    return $password;
}

/*移动端判断*/
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

