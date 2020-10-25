<?php

namespace App\Model;

use App\Model\Artbc\BtConfig;
use App\Model\Btshop\AlipayDraw;
use App\Model\Btshop\BankDraw;
use App\Model\Btshop\BlockTiqu;
use App\Model\Tender\TenderAsset;
use App\Service\SsoService;
use App\Utils\DateUtil;
use App\Utils\RedisKeys;
use App\Utils\RedisUtil;
use function GuzzleHttp\default_ca_bundle;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class Member
 *
 * @property int $id
 * @property int $wallet_invite_member_id
 * @property int $spid
 * @property string phone
 * @property string nationcode
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Model
 */
class Member extends Authenticatable
{
    use Notifiable;

    const SEX_MAN = 1;
    const SEX_FMALE = 2;

    static $current;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'password','is_lock','nickname','code','invite_code','invite_member_id', 'uid',
	    'idno', 'sec_phone', 'sex', 'tk', 'wallet_invite_member_id', 'spid', 'nationcode'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function fetchSexLabel($sex)
    {
        switch ($sex){
	        case self::SEX_MAN:
	        	return '男';
	        case self::SEX_FMALE:
	        	return'女';
	        default:
	        	return '未知';
        }
    }

    public function getAttributeSexlabel()
    {
        return static::fetchSexLabel($this->sex);
    }

	public function role() {
        return $this->hasOne("App\Model\Account");
    }

    public function account() {
        return $this->hasOne('App\Model\Account', 'member_id', 'id');
    }

    public function userProducts()
    {
    	return $this->hasMany(UserProduct::class, 'member_id', 'id');
    }

	public function assets() {
        return $this->hasManyThrough('App\Model\Asset', 'App\Model\Account');
    }

    public static function fetchIdWithPhone($phone){
        if (empty($phone)) {
        	return NULL;
        }
        $member = static::where('phone', $phone)
	        ->select('id')
	        ->first();
        if($member) {
        	return $member->id;
        }
        return NUll;
    }

    public static function fetchPhoneWithId($id){
        if (empty($id)) {
        	return NULL;
        }
        $member = static::where('id', $id)
	        ->select('phone')
	        ->first();
        if($member) {
        	return $member->phone;
        }
        return NUll;
    }

	public static function fetchIdWithInviteCode($code){
		if (empty($code)) {
			return 0;
		}
		$member = static::where('invite_code', $code)
			->select('id')
			->first();
		if (!$member) {
            $member = static::where('phone', $code)
                ->select('id')
                ->first();
        }
		if($member) {
			return $member->id;
		}
		return 0;
	}

    /**
     * @param $code
     * @return static
     */
	public static function fetchModelWithInviteCode($code)
    {
        Log::debug(__FUNCTION__ . ': ' . $code);
        if (empty($code)) {
            return null;
        }
        $member = static::where('invite_code', $code)
            ->first();
        if (!$member) {
            $member = static::where('phone', $code)
                ->first();
        }
        return $member;
    }

	/**
	 * 获取当前登录用户
	 * @desc current
	 * @return mixed
	 */
	public static function current()
	{
		$member_id = Auth::guard('front')->user()->id;
		$member = static::where('id', $member_id)->first();
		return $member;
	}

    /**
     * @return static
     */
	public static function apiCurrent()
	{
		if(empty(static::$current) || !static::$current instanceof self){
			if(empty($_SERVER['HTTP_TK'])) {
				return null;
			}
			$tk = $_SERVER['HTTP_TK'];

			static::$current = static::fetchByTk($tk);
		}
		return static::$current;
	}

	/**
	 * @desc fetchByTk
	 * @param $tk
	 * @return self
	 */
	public static function fetchByTk($tk)
	{
		return static::where('remember_token', $tk)->first();
	}

	public function tender_asset()
	{
		return $this->hasOne(TenderAsset::class, 'member_id', 'id');
	}

	/**
	 * @desc fetchModelByPhone
	 * @param $phone
	 * @return Member
	 */
	public static function fetchModelByPhone($phone)
	{
		return static::where('phone', $phone)->first();
	}

	public function parent()
	{
		return $this->hasOne(Member::class, 'id', 'invite_member_id');
	}

    /**
     * @param $id
     * @return Member
     */
	public static function walletParent($id)
    {
        if ($id === 0) {
            return null;
        }
        return static::where('id', $id)->first();
    }

    public static function walletInviteSum($id)
    {
        return static::where('wallet_invite_member_id', $id)->count('id');
    }

    public function walletSparent()
    {
        return $this->hasOne(Member::class, 'id', 'spid');
    }

    /**
     * 获取总注册量
     * @return int
     */
    public static function totalCount()
    {
        $today = DateUtil::todayDate();
        $key = RedisKeys::MEMBER_REG_SUM . $today;

        $amount = RedisUtil::get($key);
        if (empty($amount)){
            $amount = Member::where('created_at', '<', DateUtil::today())
                ->count();
            RedisUtil::set($key, $amount, 86400);
        }
        return $amount;
    }

    /**
     * 获取单日注册量
     * @return int
     */
    public static function todayCount()
    {
        $today = DateUtil::today();
        return Member::where('created_at', '>', $today)
            ->count();
    }

    public function locked()
    {
        return $this->is_lock === '1';
    }

    /**
     * 计算用户当日的 支付宝提现 + 银行卡提现 + 版通提取总价值
     * @param $mid
     */
    public static function todayDrawAmount($mid)
    {
        $alipayAmoount = AlipayDraw::todayAmount($mid);
        $bankcardAmount = BankDraw::todayAmount($mid);
        $btAmount = BlockTiqu::memberTodayAmount($mid);
        Log::debug(__FUNCTION__, compact('alipayAmoount', 'bankcardAmount', 'btAmount'));
        return $alipayAmoount + $bankcardAmount + $btAmount * BtConfig::getPrice();
    }
}

