<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2017/12/24
 * Time: 下午4:46
 */

namespace App\Utils;


use Illuminate\Database\Eloquent\Builder;

class QueryUtil
{
	/**
	 * @desc selectBuild
	 * @param Builder $query
	 * @param         $cond
	 *    example: [
	 *          ['var', '=']
	 *      ]
	 */
	public static function selectBuild($query, $data, $conds, $table='')
	{
		foreach($conds as $c){
			\Log::debug($c, [
				'column' => $c,
				'data' => $data
			]);
			if(isset($data[$c[0]])){
				$column = isset($c[2]) ? $c[2] : $c[0];
				$ope = $c[1];
				$value = $data[$c[0]];
				if (!empty($table)){
					$column = $table . '.' . $column;
				}
				\Log::debug($column, [
					'column' => $column,
					'ope' => $ope,
					'value' => $value
				]);
				if ($ope == 'in'){
					$query->whereIn($column, $value);
				}elseif($ope == 'like') {
					$query->where($c[0], 'like', "%{$value}%");
				}else{
					if (strpos($c[0], '_before') !== false){
						$value .= " 23:59:59";
					}
					if (strpos($column, '_after') !== false){
						$value .= " 00:00:00";
					}
					$query->where($column, $ope, $value);
				}
			}
		}
	}

	public static function sortBuild(&$query, $data, $conds)
	{
		foreach ($conds as $k => $c) {
			if (!empty($data[$k])) {
				$direct =  $data[$k] == 1 ? 'asc' : 'desc';
				$query->orderBy($c, $direct);
			}
		}
	}

}