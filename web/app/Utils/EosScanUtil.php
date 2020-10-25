<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-02
 * Time: 10:24
 */

namespace App\Utils;


use Illuminate\Support\Facades\Log;

class EosScanUtil
{
    const ENDPOINT = 'http://eos.artbchain.io';
    const CHAINPATH = '/v1/chain/';

    public static function getBlock($block_num)
    {
        $data = [
            'block_num_or_id' => $block_num
        ];
        $res = HttpUtil::json(self::ENDPOINT . self::CHAINPATH . 'get_block', json_encode($data));
        if (!$res){
            return null;
        }
        Log::debug('get block ' . $block_num, [
            'res' => $res
        ]);
        return json_decode($res, true);
    }
}