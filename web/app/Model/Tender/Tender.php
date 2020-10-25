<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/16
 * Time: 上午11:35
 */

namespace App\Model\Tender;


use App\Utils\DateUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
	use SoftDeletes;

	const TENDER = 0; // 暗标
	const AUCTION = 1; // 竞拍

	const STAT_TODO = -1;
	const STAT_ING = 0;
	const STAT_COUNT = 1;
	const STAT_GUESS_COUNT_FINISHED = 2;
	const STAT_DONE = 3;

	protected $fillable = ['code', 'name', 'type', 'video', 'starting_price', 'info', 'stat', 'banner', 'poster', 'guess_start', 'guess_end', 'tender_start', 'tender_end', 'deal_log_id', 'guess_count', 'valuation'];

	public function type()
	{
		return $this->type == self::TENDER ? '暗标' : '竞拍';
	}

	public function guessTime()
	{
		return $this->guess_start . '至' . $this->guess_end;
	}

	public function tenderTime()
	{
		return $this->tender_start . '至' . $this->tender_end;
	}

	public function stat()
	{
		switch ($this->stat) {
			case -1:
				return '未开始';
			case 0:
				return '拍卖中';
			case 1:
				return '奖金计算中';
			case 2:
				return '奖金计算完毕';
			default:
				return '结束';
		}
	}

	public function isPublished()
	{
		return $this->stat != -1;
	}

	/**
	 * 是否为暗标
	 * @desc isDark
	 * @return bool
	 */
	public function isDark()
	{
		return $this->type == self::TENDER;
	}

	/**
	 * 获取正在进行的拍卖
	 * @desc tendering
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function tendering()
	{
		$now = DateUtil::now();

		return static::where([['stat', '=', 0], ['type', '=', 0], ['guess_start', '<', $now], ['tender_end', '>', $now]])->orWhere([['stat', '=', 0], ['type', '=', 1], ['tender_start', '<', $now], ['tender_end', '>', $now]])->orderByDesc('created_at')->get();
	}

	/**
	 * 获取正在进行的拍卖
	 * @desc tendering
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function tendertodo()
	{
		$now = DateUtil::now();

		return static::where([['stat', '=', 0], ['type', '=', 0], ['guess_start', '>', $now],])->orWhere([['stat', '=', 0], ['type', '=', 1], ['tender_start', '>', $now],])->orderByDesc('created_at')->get();
	}

	public function guesses()
	{
		return $this->hasMany(TenderGuess::class, 'tender_id', 'id');
	}

	/**
	 * 取估价投入的30%作为第一名的资金
	 * @desc firstPrice
	 * @return float
	 */
	public function firstPrice()
	{
		return $this->guess_count * 3;
	}

	/**
	 * 取估价投入50%作为一等奖的资金
	 * @desc secondPrice
	 */
	public function secondPrice()
	{
		return $this->guess_count * 5;
	}

	/**
	 * 资金池
	 * @desc priceCount
	 * @return float
	 */
	public function priceCount()
	{
		return $this->firstPrice() + $this->secondPrice();
	}

	/**
	 * 取估价人数的10%为一等奖
	 * @desc secondPriceMembers
	 * @return int
	 */
	public function secondPriceMembers()
	{
		return intval($this->guess_count * 0.1);
	}

	/**
	 * 出价
	 * @desc offers
	 */
	public function tender_logs()
	{
		return $this->hasMany(TenderLog::class, 'tender_id', 'id');
	}

	public function deal()
	{
		return $this->hasOne(TenderLog::class, 'id', 'deal_log_id');
	}

	/**
	 * 根据估价人数计算获奖人数
	 * @desc winners
	 * @return int
	 */
	public function winners()
	{
		// 第1名加前百分之10
		$count = $this->guess_count;
		return $count > 0 ?  floor($count * 0.1) + 1 : 0;
	}

	/**
	 * 获取拍品最新出价
	 * @desc lastPrice
	 */
	public function lastPrice()
	{
		$tender_log = TenderLog::where('tender_id', $this->id)
			->orderByDesc('price')
			->first();
		return $tender_log ? $tender_log->price : $this->starting_price;
	}

	public function lastTender()
	{
		return TenderLog::where('tender_id', $this->id)
			->orderByDesc('price')
			->first();
	}

	/**
	 * 是否在估价环节
	 * @desc isGuessing
	 * @return bool
	 */
	public function isGuessing()
	{
		$now = DateUtil::now();
		return $this->isDark() && $now > $this->guess_start && $now < $this->guess_end;
	}

	public function goingTender()
	{
		$now = DateUtil::now();
		return ($this->isDark() && $now > $this->guess_end && $now < $this->tender_start) ||
			(!$this->isDark() && $now < $this->tender_start);
	}

	/**
	 * 竞拍中
	 * @desc isTendering
	 * @return bool
	 */
	public function isTendering()
	{
		$now = DateUtil::now();
		return $now > $this->tender_start && $now < $this->tender_end;
	}

	public function tenderEnded()
	{
		$now = DateUtil::now();
		return $now > $this->tender_end;
	}

	public function process()
	{
		$now = DateUtil::now();
		if($now > $this->tender_end){
			return '已结束';
		}
		if ($this->isDark()) {
			if($now < $this->guess_start) {
				return '即将开始';
			}
			if ($now > $this->guess_start && $now < $this->guess_end) {
				return '估价进行时';
			}elseif ($now > $this->tender_start && $now < $this->tender_end){
				return '暗标进行中';
			} else {
				return '暗标马上开始';
			}
		} else {
			if($now < $this->tender_start) {
				return '即将开始';
			}
			if ($now > $this->tender_start && $now < $this->tender_end){
				return '竞拍中';
			}
		}
	}

	// 如果是暗标，只能出价一次
	public function isDarkAndTenderd($member_id)
	{
		return $this->isDark() && TenderLog::where(['tender_id' => $this->id, 'member_id' => $member_id])->exists();
	}

	// 获取目前最高价
	public function maxPrice()
	{
		if ($this->isDark()) {
			return 0;
		}
		// 如果有出价取出价，如果无出价取起拍价
		$lastTender = TenderLog::where('tender_id', $this->id)
			->orderBy('price', 'desc')
			->orderBy('created_at', 'desc')
			->first();
		return empty($lastTender) ? $this->starting_price : $lastTender->price;
	}

	// 获取目前最高价
	public function dealPrice()
	{
		// 如果有出价取出价，如果无出价取起拍价
		$deal = $this->deal;
		return empty($deal) ? '暂无' : $deal->price;
	}

	public function dealMember()
	{
		// 如果有出价取出价，如果无出价取起拍价
		$deal = $this->deal;
		return empty($deal) ? '暂无' : $deal->member->phone;
	}

}