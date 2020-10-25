<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/8
 * Time: 上午11:29
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{

	const STAT_SUBMIT = 0;
	const STAT_DELIVEIED = 1;
	const STAT_REJECTED = 2;

	protected $fillable = [
		'member_id', 'name', 'phone','province', 'city', 'area', 'addr', 'stat', 'auditor', 'note', 'asset_code', 'amount'
	];

	public function project()
	{
		return $this->hasOne(Project::class, 'asset_code', 'asset_code');
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

	public function statText()
	{
		switch ($this->stat){
			case self::STAT_SUBMIT:
				return '待发货';
			case self::STAT_DELIVEIED:
				return '已发货';
			case self::STAT_REJECTED:
				return '未通过';
		}
	}
}