<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-06
 * Time: 21:26
 */

namespace App\Http\Middleware;

use App\Utils\ApiResUtil;

class Appid
{
    public function handle($request, \Closure $next)
    {
        $appids = [
            'score_asdfksjaosdi' => 'Kadfnoi1)af!_*kdafd',
            'artbc_appid' => ':kdaOOINfaoiwe10(Jk123r',
            'wallet_appid' => 'asdfasdlkIDDFsfdallisdfnlkasdf',
        ];

        $appid = $_SERVER['HTTP_APPID'];
//        $appid = $request->input('appid', '');

        if(!isset($appids[$appid])){
            echo json_encode(ApiResUtil::error('请使用艺行派访问'));
            exit;
        }
        return $next($request);
    }
}