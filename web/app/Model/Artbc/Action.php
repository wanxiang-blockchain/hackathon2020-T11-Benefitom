<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 10 Jan 2019 11:00:54 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Action
 * 
 * @property int $id
 * @property int $block_num
 * @property string $trxid
 * @property string $block_id
 * @property string $account
 * @property string $name
 * @property string $from
 * @property string $to
 * @property string $quantity
 * @property string $memo
 * @property string $timestamp
 * @property string $status
 *
 * @package App\Model\Btshop
 */
class Action extends Eloquent
{

    protected $connection = 'eosio';

	public $timestamps = false;

	protected $casts = [
		'block_num' => 'int'
	];

	protected $fillable = [
		'block_num',
		'trxid',
		'block_id',
		'account',
		'name',
		'from',
		'to',
		'quantity',
		'memo',
		'timestamp',
		'status'
	];

    /**
     * @param $tx
     * @return static
     */
	public static function fetchByTx($tx)
    {
        return static::where('trxid', $tx)->first();
    }
}
