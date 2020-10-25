<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 21 Dec 2018 10:31:33 +0800.
 */

namespace App\Model\Artbc;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class WalletInvite
 * 
 * @property int $id
 * @property int $member_id
 * @property int $pid
 * @property int $level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model\Artbc
 */
class WalletInvite extends Eloquent
{
	protected $casts = [
		'member_id' => 'int',
		'pid' => 'int',
		'level' => 'int'
	];

	protected $fillable = [
		'member_id',
		'pid',
		'level'
	];

	public static function add($mid, $pid, $level) {
	    if (static::where(['member_id' => $mid, 'pid' => $pid])->exists()){
	        return true;
        }
        return static::create([
            'member_id' => $mid,
            'pid' => $pid,
            'level' => $level
        ]);
    }

    /**
     * @param $mid
     * @param $pid
     * @return static
     */
    public static function fetchByMidPid($mid, $pid)
    {
        return static::where('member_id', $mid)
            ->where('pid', $pid)
            ->first();
    }

    /**
     * @param $mid
     * @return WalletInvite[]|\Illuminate\Database\Eloquent\Collection
     */
	public static function fetchPids($mid)
    {
        return static::where('member_id', $mid)->get();
    }

    /**
     * @param $pid
     * @return WalletInvite[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchSons($pid)
    {
        return static::where('pid', $pid)->get();
    }


}
