<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 21 Jan 2019 16:30:13 +0800.
 */

namespace App\Model\Btshop;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AlipayTn
 * 
 * @property int $id
 * @property int $tn
 * @property int $fee
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Btshop
 */
class AlipayTn extends Eloquent
{
	protected $table = 'alipay_tn';

	protected $casts = [
		'tn' => 'int',
		'fee' => 'int'
	];

	protected $fillable = [
		'tn',
		'fee'
	];
}
