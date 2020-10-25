<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Headers: Content-Type, TK");

Route::group(['middleware' => ['record.query']], function() {
    Route::post('/login', 'MemberController@login')->name('api/login');
    Route::post('/reg', 'MemberController@reg')->name('api/reg');
    Route::post('/sms', 'SmsController@index')->name('api/sms');
    Route::post('/pwd/reset', 'MemberController@resetPwd')->name('api/pwd/reset');
});

Route::post('/bi/prices', 'ArtbcController@prices')->name('api/bi/prices');
Route::group(['middleware' => ['record.query']], function() {
	Route::post('/bi/txlist', 'ArtbcController@txlist')->name('api/bi/txlist');
});
Route::group(['middleware' => ['auth.api', 'record.query']], function() {
	Route::post('/cca/create', 'ArtbcController@ccaCreate')->name('api/cca/create');
});

Route::post('/wallet/version', function (){
	return \App\Utils\ApiResUtil::ok(['v' => '9.0.2', 'intv' => 49]);
})->name('api/wallet/version');


Route::group(['middleware'=>['sign']], function (){
	Route::get('/profile', 'MemberController@profile')->name('api/profile');
});

// 实名认证必须接口
Route::group(['middleware' => ['auth.api', 'record.query', 'member.verified']], function() {
    Route::post('/artbc/ti', 'ArtbcController@ti')->name('api/artbc/ti');
    Route::post('/btscore/ti', 'BtScoreController@ti')->name('api/btscore/ti');
    Route::post('/appendInviteCode', 'MemberController@appendInviteCode')->name('api/appendInviteCode');
    Route::post('btshop/order/ti', 'BtshopController@orderTi')->name('api/btshop/order/ti');
    Route::post('block/ti', 'BlockController@ti')->name('api/block/ti');
    Route::post('block/tiCash', 'BlockController@tiCash')->name('api/block/tiCash');
    Route::post('block/toRmb', 'BlockController@toRmb')->name('api/block/toRmb');
    Route::post('block/sale', 'BlockController@saleARTTBC')->name('api/block/sale');
    Route::post('block/tibi', 'BlockController@tibi')->name('api/block/tibi');

    // bank cards
    Route::post('/card/bind', 'BankCardController@bind')->name('api/bankcard/bind');
    Route::post('/card', 'BankCardController@index')->name('api/card');
    Route::post('/bankcard/draw', 'BankCardController@draw')->name('api/bankcard/draw');

    // alipay
    Route::post('/alipay/bind', 'AlipayController@bind')->name('api/alipay/bind');
    Route::post('/alipay/tn', 'AlipayController@tn')->name('api/alipay/tn');
    Route::post('/alipay/draw', 'AlipayController@draw')->name('api/alipay/draw');

    // bt account
    Route::post('/btaccount/bind', 'BtAccountController@bind')->name('api/btaccount/bind');

});

Route::group(['middleware' => ['auth.api', 'record.query']], function(){

    Route::post('/alipay/info', 'AlipayController@index')->name('api/alipay/info');
    Route::post('/btaccount/info', 'BtAccountController@index')->name('api/btaccount/info');   Route::post('tmp/tk', 'MemberController@tmpTk')->name('tmp/tk');

	Route::post('/artbc/unlock/info', 'ArtbcUnlockController@info')->name('api/artbc/unlock/info');

    Route::post('/btscore/unlock/info', 'BtScoreUnlockController@info')->name('api/btscore/unlock/info');
    Route::post('/btscore/unlocks', 'BtScoreUnlockController@index')->name('api/btscore/unlocks');
    Route::post('/btscore/logs', 'BtScoreController@logs')->name('api/btscore/logs');

    Route::post('/msg/list', 'PushController@index')->name('api/msg/list');
    Route::post('/msg/{id}', 'PushController@detail')->name('api.msg.detail');
    Route::post('/msg/read', 'PushController@read')->name('api/msg/read');

    Route::post('/myprofile', 'MemberController@myProfile')->name('api/myprofile');
    Route::post('/cash/balance', 'MemberController@balance')->name('api/cash/balance');

    Route::post('/mywalletinvite', 'MemberController@myWalletInvite')->name('api/mywalletinvite');
    Route::post('/subinvite', 'MemberController@subinvite')->name('api/subinvite');

    Route::post('btshop/products', 'BtshopController@products')->name('api/btshop/products');
    Route::post('btshop/product', 'BtshopController@product')->name('api/btshop/product');
//    Route::post('btshop/order/make', 'BtshopController@orderMake')->name('api/btshop/make');
//    Route::post('btshop/order/done', 'BtshopController@orderDone')->name('api/btshop/done');
    Route::post('btshop/orders', 'BtshopController@orders')->name('api/btshop/orders');
    Route::post('btshop/order', 'BtshopController@order')->name('api/btshop/order');

    Route::post('block/prerecharge', 'BlockController@prerecharge')->name('api/block/prerecharge');
    Route::post('block/recharge', 'BlockController@recharge')->name('api/block/recharge');
    Route::post('/block/logs', 'BlockController@logs')->name('/api/block/logs');
    Route::post('block/lastPrice', 'BlockController@lastPrice')->name('/api/block/lastPrice');
    Route::post('block/transfer', 'BlockController@transfer')->name('api/block/transfer');
    Route::post('block/pay', 'BlockController@pay')->name('api/block/pay');

    // eosio
    Route::post('/cca/logs', 'CcaController@index')->name('cca/logs');

    // finance
    Route::post('/finance/logs', 'FinanceController@index')->name('api.finance.logs');

    // send verify sms
    Route::post('/send/verify/sms', 'SmsController@verifySms')->name('api/verifySms');

    // osstk
    Route::post('/oss/tk', 'OssController@sts')->name('api.oss.tk');

    Route::post('/userinfo/verify', 'ProfileController@verify')->name('api.userinfo.verify');
    Route::post('/user/verify/info', 'ProfileController@userinfo')->name('api.user.verify.info');
});
