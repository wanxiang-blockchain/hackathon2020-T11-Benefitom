<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 13 Feb 2019 11:25:14 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ProfileLog
 * 
 * @property int $id
 * @property int $member_id
 * @property string $name
 * @property string $idno
 * @property int $sex
 * @property string $id_img
 * @property string $id_back_img
 * @property string $id_hold_img
 * @property int $verified
 * @property string $auditor
 * @property string $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @package App\Model\Btshop
 */
class ProfileLog extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	use HasMemberTrait;

	const VERIFIED_INIT = 0;  // 待审核
	const VERIFIED_DONE = 1;  // 认证通过
	const VERIFIED_REJECT = 2;  // 审核未通过

    const SEX_FEMALE = 0;
    const SEX_MALE = 1;

	protected $casts = [
		'member_id' => 'int',
		'verified' => 'int',
        'sex' => 'int'
	];

	protected $fillable = [
		'member_id',
		'name',
		'idno',
        'sex',
		'id_img',
		'id_back_img',
		'id_hold_img',
		'verified',
		'auditor',
		'note'
	];

	public static function verifyLabel($verified)
    {
        switch ($verified){
            case self::VERIFIED_INIT:
                return '待审核';
            case self::VERIFIED_DONE:
                return '认证成功';
            case self::VERIFIED_REJECT:
                return '审核未通过';
        }
    }

    public static function sexLabel($sex)
    {
        switch ($sex){
            case self::SEX_FEMALE:
                return '女';
            case self::SEX_MALE:
                return '男';
        }
    }

    /**
     * @param $mid
     * @return static
     */
    public static function fetchLastByMid($mid)
    {
        return static::where('member_id', $mid)
            ->orderByDesc('created_at')->first();
    }

    /**
     * @param $mid
     * @param $verified
     * @return static
     */
	public static function fetchByMidVerified($mid, $verified)
    {
        return static::where('member_id', $mid)
            ->where('verified', $verified)
            ->first();
    }

    public static function idnoUsed($idno)
    {
        return static::where('idno', $idno)
            ->whereIn('verified', [self::VERIFIED_INIT, self::VERIFIED_DONE])
            ->exists();
    }
}
