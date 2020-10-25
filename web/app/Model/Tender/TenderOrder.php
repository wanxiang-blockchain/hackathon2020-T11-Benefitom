<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/22
 * Time: 上午11:55
 */

namespace App\Model\Tender;


use Illuminate\Database\Eloquent\Model;

class TenderOrder extends Model
{
	protected $fillable = [
		'order_id', 'member_id', 'amount', 'stat', 'type', 'wxdata'
	];
}