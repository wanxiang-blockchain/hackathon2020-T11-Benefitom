<?php
/**
 * Created by PhpStorm.
 * User: justshaw
 * Date: 2019-01-06
 * Time: 20:53
 */

namespace App\Utils;


class RedisKeys
{

    // 积分系统临时tk前缀
    const DIS_VERIFY_TK_PRE = 'dis:tk:';

    // 支付宝提现限额
    const ALIPAY_DRAW_AMOUNT_LIMIT_PRE = 'ali:draw:amount:limit:';

    // 支付宝单日提现次数限制
    const ALIPAY_DRAW_DAY_TIMES_LIMIT_PRE = 'ali:draw:day:times:limit:';

    // arttbc 购买统计数据
    const BTSHOP_ORDER_BT_SUM = 'btshop:order:bt:sum:';
    // artbc 购买统计数据
    const BTSHOP_ORDER_ARTBC_SUM = 'btshop:order:artbc:sum:';
    // rmb 购买统计数据
    const BTSHOP_ORDER_RMN_SUM = 'btshop:order:rmb:sum:';
    // artbcs 购买统计数据
    const BTSHOP_ORDER_ARTBCS_SUM = 'btshop:order:artbcs:sum:';
    // 注册用户统计
    const MEMBER_REG_SUM = 'member:reg:sum:';
    // 注册用户统计
    const ALIPAY_DRAW_SUM = 'alipay:draw:sum:';
    // 支付宝充值
    const ALIPAY_RECHARGE_SUM = 'alipay:recharge:sum:';

    // 频次限制
    const API_PER_TIMES_LIMIT_PRE = 'api:per:times:limit:pre';

    // 邀请用户处理
    const WALLET_INVITE_FLUSH_LIST = 'wallet:invite:flush:list';

    // 报警队列
    const WARING_EMAIL_LIST = 'waring:email:list';

}