<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/26
 * Time: 上午10:00
 */

namespace App\Model\Tender;


use Illuminate\Database\Eloquent\Model;

class TenderBroadcastRead extends Model
{
	protected $table = 'tender_broadcast_read';
	protected $fillable = ['memer_id', 'broad_id'];

	public static function read($member_id, $broadcast_id)
	{
		$model = static::where('member_id', $member_id)
			->where('broad_id', $broadcast_id)
			->first();
		if(!$model){
			$model = new static();
			$model->member_id = $member_id;
			$model->broad_id = $broadcast_id;
			return $model->save();
		}
		return true;
	}
}