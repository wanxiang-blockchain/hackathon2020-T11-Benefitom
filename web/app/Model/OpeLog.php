<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OpeLog extends Model
{
    //

	protected $fillable = [
		'oprator', 'con', 'data', 'keyword'
	];

	/**
	 * @desc record
	 * @param $con String
	 * @param $data Array
	 * @return $this|Model
	 */
	public static function record( $con, $data, $keyword='')
	{
		return self::create([
			'oprator' => Auth()->id(),
			'con' => $con,
			'data' => json_encode($data),
			'keyword' => $keyword
		]);
	}
}
