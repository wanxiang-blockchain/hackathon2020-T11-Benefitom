<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 24 Apr 2018 11:47:00 +0800.
 */

namespace App\Model;

use App\Exceptions\TradeException;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Artbc
 * 
 * @property int $id
 * @property int $member_id
 * @property float $balance
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Artbc extends Eloquent
{

	const GIFT_PROPORTION = 2.01;   // 赠币比例

	protected $casts = [
		'member_id' => 'int',
		'balance' => 'float'
	];

	protected $fillable = [
		'member_id',
		'balance'
	];

	public static function add($member_id, $amount)
	{
		$model = static::where('member_id', $member_id)->first();
		if (!$model){
			$model = new static();
			$model->member_id = $member_id;
		}
		$model->balance += $amount;
		if (!$model->save()){
			throw new TradeException('修改用户代币余额失败');
		}
		return $model->balance;
	}

	/**
	 * @desc fetchByMemberId
	 * @param $member_id
	 * @return static
	 */
	public static function fetchByMemberId($member_id)
	{
		return static::where('member_id', $member_id)->first();
	}

	/**
	 * @desc fetcyBalanceByMemberId
	 * @param $member_id
	 * @return int
	 */
	public static function fetcyBalanceByMemberId($member_id)
	{
		$model = static::fetchByMemberId($member_id);
		return empty($model) ? 0 : $model->balance;
	}

    public static function giftRate(){
        try{
		    $file = '/tmp/artb_price_tmp';
            $json = 'https://ta.wenbantong.com/front/hq/delay_hq.json';
            $stockHtml = @file_get_contents($json);
            $price = 3.9;
            $stockHtml = str_replace('jsonpCallback(', '', $stockHtml);
            $stockHtml = str_replace(')', '', $stockHtml);
            $stockHtml = json_decode($stockHtml, true);
            $stockHtml = $stockHtml[0]['stockHtml'];
            if (strpos($stockHtml, 'ARTTBC') === false){
                $price = @file_get_contents($file);
                if (is_numeric($price)) {
                    return $price;
                }else{
                    return 3.9;
                }
            }
            $trs = explode('</tr>', $stockHtml);
            $tr = null;
            foreach ($trs as $i => $v) {
                if (strpos($v, '艺行通版通') !== false) {
                    $tr = $v;
                    break;
                }
            }
            if (!$tr){
                return $price;
            }
            $tds = explode('</td>', $tr);
            if (empty($tds[5])) {
                return $price;
            }
            $line = explode('>', $tds[5]);
            if (isset($line[1]) && is_numeric($line[1])) {
                $price = $line[1];
            }
            file_put_contents($file, $price);
            return $price;
        }catch (\Exception $e) {

        }

	}

}
