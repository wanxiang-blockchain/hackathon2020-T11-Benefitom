<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-02-11
 * Time: 11:29
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Utils\OssUtil;
use App\Utils\ResUtil;

class OssController extends Controller
{
    public function sts()
    {
        $type = request()->get('type', 2);
        return ResUtil::ok(OssUtil::fetchSts($type));
    }

    public function sign()
    {
        $uri = request()->get('uri');
        return ResUtil::ok(OssUtil::fetchGetSignUrl($uri));
    }
}