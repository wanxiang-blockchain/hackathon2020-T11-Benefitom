<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 13 Feb 2019 11:25:07 +0800.
 */

namespace App\Model;

use App\Exceptions\TradeException;
use App\Model\Btshop\AlipayDraw;
use App\Utils\DateUtil;
use App\Utils\OssUtil;
use Psy\Exception\TypeErrorException;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Profile
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
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @package App\Model\Btshop
 */
class Profile extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	use HasMemberTrait;

	protected $casts = [
		'member_id' => 'int',
		'verified' => 'int',
        'sex' => 'int'
	];

	protected $fillable = [
		'member_id',
		'name',
		'idno',
		'id_img',
		'id_back_img',
		'id_hold_img',
		'verified',
        'sex'
	];

	public static function isMemberVerified($mid)
    {
        return static::where('member_id', $mid)->exists();
    }

    /**
     * @param $mid
     * @return static
     */
    public static function fetchByMid($mid)
    {
        return static::where('member_id', $mid)->first();
    }

    public static function fetchOssSign($profile)
    {
        if (!$profile instanceof Profile && !$profile instanceof ProfileLog){
            throw new TypeErrorException('unexcept profile');
        }
        $profile->id_img = OssUtil::fetchGetSignUrl($profile->id_img);
        $profile->id_back_img = OssUtil::fetchGetSignUrl($profile->id_back_img);
        $profile->id_hold_img = OssUtil::fetchGetSignUrl($profile->id_hold_img);
        return $profile;
    }

}
