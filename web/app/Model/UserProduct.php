<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/30
 * Time: 下午3:27
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
	protected $fillable = [
		'member_id', 'product_id', 'stat', 'amount', 'end_at'
	];

	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}

	public function member()
	{
		return $this->hasOne(Member::class, 'id', 'member_id');
	}

	public function earnings()
	{
		return $this->amount * $this->product->price * $this->product->rate * $this->product->duration / 12;
	}
}