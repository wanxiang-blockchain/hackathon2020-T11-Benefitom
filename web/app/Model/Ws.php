<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/9/27
 * Time: 下午3:07
 */

namespace App\Model;


use App\Helpers\GenerateCode;
use Illuminate\Database\Eloquent\Model;

class Ws extends Model
{
	protected $fillable = [
		'member_id', 'tk', 'asset_type_id'
	];

	/**
	 * @desc createTk
	 * @return string
	 */
	public static function createTk()
	{
		return GenerateCode::generate(['length' => 40]) . ( time() + 86400 );
	}

	public static function add($member_id, $asset_type_id)
	{
		$tk = static::createTk();
		static::create([
			'member_id' => $member_id,
			'tk' => $tk,
			'asset_type_id' => $asset_type_id
		]);
		return $tk;
	}

	public function verifyTk()
	{
		return substr($this->tk, -10) < time();
	}

}