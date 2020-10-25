<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/11/15
 * Time: 下午4:35
 */

namespace App\Model;


trait HasMemberTrait
{
	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}
}