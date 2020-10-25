<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 2018/7/9
 * Time: 下午2:34
 */

namespace App\Http\Controllers\Api;


use App\Exceptions\TradeException;
use App\Http\Controllers\Controller;
use App\Model\Artbc;
use App\Model\ArtbcLog;
use App\Model\Member;
use App\Utils\ApiResUtil;
use App\Utils\ResUtil;
use EasyWeChat\Payment\API;
use Illuminate\Http\Request;

class ArtbcController extends Controller
{

	const APPID = 'asdfasdlkIDDFsfdallisdfnlkasdf';

	public function info()
	{
		$member = Member::apiCurrent();
	}

	public function ti(Request $request)
	{
		$member = Member::apiCurrent();
		$amount = $request->get('amount');
		$eth_addr = $request->get('eth_addr');

		if (empty($amount) || empty($eth_addr)) {
			return ApiResUtil::error('请输入提取信息');
		}

		if (empty($amount) || $amount < 100) {
			return ApiResUtil::error('单次提币不得少于100');
		}

		if (strtolower(substr($eth_addr, 0, 2)) !== '0x' || strlen($eth_addr) !== 42) {
			return ApiResUtil::error('钱包地址格式不正确');
		}

		\DB::beginTransaction();
		try {
			// 添加artbc_log，artbc
			$artbc = Artbc::fetchByMemberId($member->id);
			if (!$artbc || $artbc->balance < $amount) {
				throw new TradeException('持币数量不够');
			}
			ArtbcLog::add($member->id, -1 * $amount, ArtbcLog::TYPE_TIBI, $eth_addr);
			\DB::commit();
			return ApiResUtil::ok();
		} catch (TradeException $e) {
			\DB::rollBack();
			return ApiResUtil::error( $e->getMessage());
		} catch (\Exception $e) {
			\Log::error($e->getTraceAsString());
			\DB::rollBack();
			return ApiResUtil::error( $e->getMessage());
		}

	}

	public function prices()
	{
		// {"ticker":{"vol":"347448.935","last":"1407.25","sell":"1407.51","buy":"1407.25","high":"1416.92","low":"1390.09"},"date":"1539845545538"}
		$json = file_get_contents('http://api.zb.cn/data/v1/ticker?market=eth_qc');
		$arr = json_decode($json, true);
		return ApiResUtil::ok([
			'eth' => isset($arr['ticker']['last']) ? $arr['ticker']['last'] : 0,
			'artbc' => strval(Artbc\BtConfig::getPrice())
		]);
	}

	public function txlist(Request $request)
	{
		$appid = self::APPID;
		$data = $request->all();
		if (!isset($data['appid']) || $data['appid'] !== $appid) {
			return ApiResUtil::error('请在艺行派客户端访问');
		}
		$query = http_build_query($data);
		$res = @file_get_contents('http://api.etherscan.io/api?' . $query . '&apikey=V72IGP54CWSNI1UXR93TQP46NQ79VSWGAY');
		return ApiResUtil::ok(json_decode($res, true));
	}

	public function ccaCreate(Request $request)
	{
		$appid = self::APPID;
		$data = $request->all();
		if (!isset($data['appid']) || $data['appid'] !== $appid) {
			return ApiResUtil::error('请在艺行派客户端访问');
		}
		if (empty($data['account'])) {
			return ApiResUtil::error('账户名不得为空');
		}
		if (!preg_match('/^[a-z1-5]{12}$/', $data['account'])) {
			return ApiResUtil::error('CCA账户名为12位长，字母+（1-5）数字');
		}
		$url = 'https://api.ccachain.info/v1/eos/faucet/create-account?account=';
		$res = @file_get_contents($url . $data['account']);
		return ApiResUtil::ok(json_decode($res, true));
	}

}