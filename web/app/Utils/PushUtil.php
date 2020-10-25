<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/1/21
 * Time: 上午11:33
 */

namespace App\Utils;


use App\Model\Btshop\BlockAsset;
use App\Model\Btshop\BlockAssetLog;
use App\Model\BlockTransferLog;
use App\Model\Cms\Push;
use App\Model\Member;
use JPush\Client;

class PushUtil
{

    const KEY = 'f008a5f2f5dbf7b0d669d482';
    const SECRET = 'bacf1dd381318e8c2d02a4a0';

    public static function push($ids, Push $push)
    {
        $prod = env('APP_ENV', 'local') == 'prod';
        $prod = false;
        $client = new Client(static::KEY, static::SECRET, storage_path() . '/logs/jpush.log');
        $pusher = $client->push();
        $pusher->setPlatform('all')
            ->addAlias($ids)
//            ->iosNotification($push->con, [
//                'extras' => [
//                    'id' => $push->id,
//                    'title' => $push->title,
//                    'subtitle' => $push->subtitle,
//                    'push_at' => $push->push_at->toDateTimeString(),
//                    'con' => $push->con,
//                    'readed' => 0
//                ]
//            ])
            ->androidNotification($push->title, [
                'extras' => [
                    'id' => $push->id,
                    'title' => $push->title,
                    'subtitle' => $push->subtitle,
                    'push_at' => $push->push_at->toDateTimeString(),
                    'con' => $push->con,
                    'readed' => 0
                ]
            ])->message($push->title, [
                'extras' => [
                    /**
                     * "id": 1,
                    "title": "Notice | We just distribute wallet version 1.0",
                    "subtitle": "On the day of Spring, We deploy a wallet",
                    "con": "This is the main text",
                    "push_at": "This is the notice time",
                    "readed": 0 // 1 readed 0 not read
                     */
                    'id' => $push->id,
                    'title' => $push->title,
                    'subtitle' => $push->subtitle,
                    'push_at' => $push->push_at->toDateTimeString(),
                    'con' => $push->con,
                    'readed' => 0
                ]
            ])->setOptions($push->id, 86400, null, $prod);
        try {
            return $pusher->send();
        } catch (\JPush\Exceptions\JPushException $e) {
            \Log::error($e->getTraceAsString());
            return false;
        }
    }

    public static function blockTransferIn(BlockTransferLog $blockTransferLog)
    {
        $phone = Member::fetchPhoneWithId($blockTransferLog->inner);
        $outer = Member::fetchPhoneWithId($blockTransferLog->outer);
        $coin = BlockAssetLog::codeToName($blockTransferLog->code);
        $con = '尊敬的用户，您的账号"****' . substr($phone, -4) . '"由用户"****' . substr($outer, -4) .'"转入' . $blockTransferLog->amount . $coin;
        $push = new \App\Model\Cms\Push();
        $push->fill([
            'type' => Push::TYPE_BLOCK_TRANSFER_IN,
            'con_id' => $blockTransferLog->id,
            'con' => $con,
            'title' => $coin . '转入通知',
            'subtitle' => $con,
            'push_to' => $phone,
            'push_at' => DateUtil::now(),
            'stat' => 1
        ]);
        $push->save();
        static::push([$phone], $push);
    }

}