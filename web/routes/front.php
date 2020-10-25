<?php

use Illuminate\Http\Request;

Route::group(['middleware'=>['throttle:1,0.1', 'member:front', 'filters']], function (){
	// 认购
    Route::post('/subscription/pay', 'SubscriptionController@pay')->name('subscription/pay');
    Route::post('/subscription/qcashpay', 'SubscriptionController@qcashpay')->name('subscription/qcashpay');
});

Route::group(['middleware'=>['sign']], function (){
	// 积分相关
//	Route::post('/score', 'ScoreController@get')->name('/score');
//	Route::post('/score/consume', 'ScoreController@consume')->name('/score/consume');
	// 直接将积分消费换成artbc消费
	Route::post('/score', 'ArtbcController@balance')->name('/score');
	Route::post('/score/consume', 'ArtbcController@consume')->name('/score/consume');

});

Route::get('/sendSms', 'LoginController@sendSms')->name('sendSms');
Route::get('/sendSmsNoAuth', 'LoginController@sendSmsNoAuth')->name('sendSmsNoAuth');
Route::get('/member/Kchart', 'MemberController@Kchart')->name('member/Kchart');
Route::get('/member/Kchart1', 'MemberController@Kchart_demo')->name('member/Kchart1');

Route::get('/chart/min', 'ChartController@min')->name('chart/min');
Route::get('/chart/k', 'ChartController@k')->name('chart/k');
Route::get('/chart/tradeTable', 'ChartController@tradeTable')->name('chart/tradeTable');
Route::get('/trade/ajaxDetail/{id}', 'TradeController@ajaxDetail')->name('trade/ajaxDetail');

// 微信支付回调
Route::post('/wxpay/callback', 'WxController@callback')->name('wxpay/callback');

Route::get('/tt', function()
{
	var_dump(Auth::guard('front')->check());
	return __FILE__;
});


Route::group(['middleware'=>[ 'filters']], function (){
    Route::post('/trade/tradeOrder', 'TradeController@tradeOrder')->name('trade/tradeOrder');
});

Route::group(['middleware' => ['member:front', 'sso', 'filters']], function () {

	// 实名
	Route::group(['middleware' => ['verifyid']], function() {
		Route::get('/member', 'MemberController@index')->name('member/index');
		Route::get('/member/withdraw', 'MemberController@withdraw')->name('member/withdraw');
	});

    Route::post('/subscription/need', 'SubscriptionController@need')->name('subscription/need');
    Route::get('/subscription/subSuccess', 'SubscriptionController@subSuccess')->name('subscription/subSuccess');

    //用户管理中心
    Route::get('/member/subscription', 'MemberController@subscription')->name('member/subscription');
    Route::get('/member/entrust', 'MemberController@entrust')->name('member/entrust');
    Route::get('/member/trade', 'MemberController@trade')->name('member/trade');
    Route::get('/member/recharge', 'MemberController@recharge')->name('member/recharge');
    Route::get('recharge', 'MemberController@recharge')->name('recharge');
    Route::get('/member/flow', 'MemberController@flow')->name('member/flow');
    Route::get('/member/setting', 'MemberController@setting')->name('member/setting');
    Route::get('/member/oneChangePhone', 'MemberController@oneChangePhone')->name('member/oneChangePhone');
    Route::post('/member/editOneChangePhone', 'MemberController@editOneChangePhone')->name('member/editOneChangePhone');
    Route::get('/member/twoChangePhone', 'MemberController@twoChangePhone')->name('member/twoChangePhone');
    Route::post('/member/editTwoChangePhone', 'MemberController@editTwoChangePhone')->name('member/editTwoChangePhone');
    Route::get('/member/resetTradePassword', 'MemberController@resetTradePassword')->name('member/resetTradePassword');
    Route::get('/member/resetTradePasswordSuccess', 'MemberController@resetTradePasswordSuccess')->name('member/resetTradePasswordSuccess');
    Route::post('/member/editResetTradePassword', 'MemberController@editResetTradePassword')->name('member/editResetTradePassword');
    Route::get('/member/asset', 'MemberController@asset')->name('member/asset');
    Route::get('/pay/getRecharge', 'PayController@getRecharge')->name('pay/getRecharge');
    Route::post('/member/postWithDraw', 'MemberController@postWithDraw')->name('member/postWithDraw');
    Route::get('/trade/inverted', 'TradeController@revoked')->name('trade/inverted');
    Route::get('/member/invite', 'MemberController@invite')->name('member/invite');
	Route::get('/member/subinvite/{phone}', 'MemberController@subinvite')->name('member/subinvite');
	Route::get('/member/delivery/{id}', 'MemberController@delivery')->name('member/delivery');
	Route::post('/member/delivery', 'MemberController@postDelivery')->name('member/delivery');
	Route::get('/member/deliveries', 'MemberController@deliveries')->name('member/deliveries');

	// 用户信息
	Route::get('/member/userinfo', 'MemberController@userinfo')->name('member/userinfo');
	Route::get('/member/userinfoEdit', 'MemberController@userinfoEdit')->name('member/userinfoEdit');
	Route::post('/member/userinfoEdit', 'MemberController@userinfoEdit')->name('member/userinfoEditPost');

	// 委托撤单
	Route::get('/trade/myentrust/{code}', 'TradeController@myentrust')->name('/trade/myentrust');

	// artbc
	Route::get('/member/artbc/ti', 'ArtbcController@ti')->name('member/artbc/ti');
	Route::post('/member/artbc/ti', 'ArtbcController@ti')->name('member/artbc/ti');
	// artbc 流水
	Route::get('/artbc/flows', 'ArtbcController@flows')->name('artbc/flowsflowsflows');

	// 收货地址
	Route::get('/addr/index', 'AddrController@index')->name('addr/index');
	Route::post('/addr/edit', 'AddrController@edit')->name('addr/edit');

	// 微信充值
	Route::post('/wx/recharge', 'WxController@recharge')->name('wx/recharge');

});

Route::group(['middleware' => ['filters']], function () {

	Route::get("/tang", 'WelcomeController@tang')->name('tang');

	Route::get('/trade', 'TradeController@index')->name('trade');

	Route::get('/trade', 'TradeController@index')->name('trade');
	Route::get('/mtrade', 'TradeController@mindex')->name('mtrade');

    Route::any('/pay/notify', 'PayController@notify')->name('pay/notify');
    Route::get('/pay/back', 'PayController@back')->name('pay/back');
    Route::any("/", 'WelcomeController@index')->name('index');
    Route::get('/login', 'LoginController@getLogin')->name('getLogin')->middleware('sso');
    Route::get('/logout', 'LoginController@logout')->name('logout');
    Route::post('/login', 'LoginController@login')->name('login');
    Route::get('/logSuccess', 'LoginController@logSuccess')->name('logSuccess');
    Route::get('/register', 'LoginController@getRegister')->name('getRegister');
    Route::post('/register', 'LoginController@postRegister')->name('postRegister');
    Route::get('/registerSuccess', 'LoginController@registerSuccess')->name('registerSuccess');
    Route::get('/getOneForget', 'LoginController@getOneForget')->name('getOneForget');
    Route::get('/getTwoForget', 'LoginController@getTwoForget')->name('getTwoForget');
    Route::get('/getThreeForget', 'LoginController@getThreeForget')->name('getThreeForget');
    Route::post('/oneForget', 'LoginController@oneForget')->name('oneForget');
    Route::post('/twoForget', 'LoginController@twoForget')->name('twoForget');
    Route::post('/threeForget', 'LoginController@threeForget')->name('threeForget');
    Route::get('/captcha', 'LoginController@captcha')->name('captcha');
    Route::get('/subscription', 'SubscriptionController@index')->name('subscription');
    Route::get('/subscription/detail/{id}', 'SubscriptionController@detail')->name('subscription/detail');

    Route::get('/novice', 'WelcomeController@novice')->name('novice');
	Route::get('/partner', 'WelcomeController@partner')->name('partner');
	Route::get('/exchange', 'WelcomeController@exchange')->name('exchange');

    Route::get('/about/company', 'AboutController@index')->name('about/company');
    Route::get('/about/media', 'AboutController@media')->name('about/media');
    Route::get('/about/industry', 'AboutController@industry')->name('about/industry');
    Route::get('/about/dynamic', 'AboutController@dynamic')->name('about/dynamic');
    Route::get('/about/notice', 'AboutController@notice')->name('about/notice');
	Route::get('/about/analysis', 'AboutController@analysis')->name('about/analysis');
    Route::get('/about/joinUs', 'AboutController@joinUs')->name('about/joinUs');
    Route::get('/about/contactUs', 'AboutController@contactUs')->name('about/contactUs');
    Route::get('/about/article/detail/{id}', 'AboutController@articleDetail')->name('article/detail');
    Route::get('/about/helpCenter', 'AboutController@helpCenter')->name('about/helpCenter');
    Route::get('/articlePreview/{id}', 'AboutController@articlePreview')->name('articlePreview');

    Route::get('/knowUs', 'WelcomeController@knowUs')->name('knowUs');
    Route::get('/artists', 'WelcomeController@artists')->name('artists');
    Route::get('/power', 'WelcomeController@power')->name('power');

    Route::get('/xieYi', 'LoginController@xieYi')->name('xieYi');
    Route::get('/sub_xieYi', function () {
        return view('front/sub_xieYi');
    })->name('sub_xieYi');
    Route::get('/trade_xieYi', function () {
        return view('front/trade_xieYi');
    });
    Route::post('jsApi', 'MemberController@jsApi')->name('jsApi');

    // 微信引导页
	Route::get('/pay/wxguide', 'PayController@wxguide')->name('wxguide');

	// 交易中心还是不必须登录吧
	Route::get('/trade/detail/{id}', 'TradeController@detail')->name('trade/detail');

});
Route::any('wechatNotify', 'PayController@wechatNotify')->name('wechatNotify');

use App\Service\PinAnBankService;
Route::any('test', function(){
    $demo =  new PinAnBankService();
    //return $demo->openCard();
    return $demo->cardList();

});
Route::any('back', function(){
    return 'back hear';

});
Route::any('back_notify', function(){
    DB::table('logs')->insert(['cont'=>var_export(request()->all(), true)]);
});
Route::get('wallet/download', function (){
	return view('front.wallet.download');
});

Route::get('wallet/sum/{tmptk}', 'WalletController@sum')->name('wallet.sum');
Route::get('wallet/user/protocol', function(Request $request){
    return view('front.wallet.protocol');
});

