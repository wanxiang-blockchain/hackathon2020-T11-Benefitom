<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//devlopment env
Route::post('login', "AuthController@postLogin")->name('admin.login.post');
Route::get('login', "AuthController@getLogin")->name('/admin/login');
Route::get('logout', "AuthController@logout");
Route::get("/", "AuthController@getLogin")->name('login');

Route::group(['middleware' => ['curmenu', 'admin']], function () {
    Route::any('/osstk', 'OssController@sts')->name('/admin/oss/sts');
    Route::get("hello", "HelloController@hello");
    Route::get("project", "ProjectController@index")->name('project');
    Route::get("project/create", "ProjectController@getcreate")->name('project/create');
    Route::post("project/create", "ProjectController@create");
    Route::get("project/edit", "ProjectController@getEdit")->name('project/edit');
    Route::post("project/postEdit", "ProjectController@postEdit")->name('project/postEdit');
    Route::get("project/delete", "ProjectController@delete")->name('project/delete');
    Route::post("project/change", "ProjectController@change")->name('project/change');
    Route::get("projectOrder", "ProjectOrderController@projectOrder")->name('projectOrder');
    Route::get("projectOrder/create", "ProjectOrderController@getCreate")->name('projectOrder/getCreate');
    Route::post("projectOrder/create", "ProjectOrderController@create")->name('projectOrder/create');
    Route::post("projectOrder/change", "ProjectOrderController@change")->name('projectOrder/change');
    Route::get("message", "HelloController@hello");
    Route::get("bank", "HelloController@hello");
    Route::get("create_bank", "HelloController@hello");
    Route::get("manage/user", "UserController@index")->name('manage/user');
    Route::get("manage/delete", "UserController@delete")->name('manage/delete');
    Route::get("manage/create", "UserController@getCreate")->name('manage/create');
    Route::post("manage/create", "UserController@create");
    Route::get("manage/edit", "UserController@getEdit")->name('manage/edit');
    Route::post("manage/postEdit", "UserController@postEdit")->name('manage/postEdit');
    Route::get("member", "MemberController@index")->name('admin/member/index');
    Route::get("member/create", "MemberController@getCreate")->name('member/create');
    Route::post("member/create", "MemberController@create");
    Route::get("member/edit", "MemberController@getEdit")->name('member/edit');
    Route::post("member/postEdit", "MemberController@postEdit")->name('member/postEdit');
    Route::post("member/change", "MemberController@change")->name('member/change');
    Route::get("member/detail/{id}", "MemberController@detail")->name('member/detail');
    Route::get("category", "CategoryController@index")->name('category');
    Route::match(['post','get'],"category/create", "CategoryController@create")->name('category/create');
    Route::match(['post','get'],"category/edit", "CategoryController@edit")->name('category/edit');
    Route::get("category/delete", "CategoryController@delete")->name('category/delete');
    Route::get("article", "ArticleController@index")->name('article/index');
    Route::get("article/create", "ArticleController@getCreate");
    Route::post("article/create", "ArticleController@create")->name('article/create');

    Route::get("slide", "SlideController@index")->name('slide');
    Route::get("slide/create", "SlideController@getCreate")->name('slide/getCreate');
    Route::post("slide/create", "SlideController@create")->name('slide/create');
    Route::get("slide/edit", "SlideController@getEdit")->name('slide/getEdit');
    Route::post("slide/edit", "SlideController@edit")->name('slide/edit');
    Route::get("slide/delete", "SlideController@delete")->name('slide/delete');

    Route::get("link", "LinkController@index")->name('link');
    Route::get("link/create", "LinkController@getCreate")->name('link/getCreate');
    Route::post("link/create", "LinkController@create")->name('link/create');
    Route::get("link/edit", "LinkController@getEdit")->name('link/getEdit');
    Route::post("link/edit", "LinkController@edit")->name('link/edit');
    Route::get("link/delete", "LinkController@delete")->name('link/delete');


    Route::get("article/delete", "ArticleController@delete")->name('article/delete');
    Route::post("article/postEdit", "ArticleController@edit")->name('article/postEdit');
    Route::get("article/edit", "ArticleController@getEdit")->name('article/edit');
    Route::post("article/change", "ArticleController@change")->name('article/change');


    Route::get("trade", "TradeController@index")->name('trade/index');
    Route::get("trade/tradeLog", "TradeController@tradeLog")->name('trade/tradeLog');
    Route::post("trade/revoked/{id}", "TradeController@revoked")->name('trade/revoked');
    Route::get("trade/set", "TradeController@set")->name('trade/set');
    Route::post("trade/setPost", "TradeController@setPost")->name('trade/setPost');


    Route::get("finance/recharge", "FinanceController@recharge")->name('finance/recharge');
    Route::get("finance/addRecharge", "FinanceController@addRecharge")->name('finance/addRecharge');
    Route::post("finance/addRecharge", "FinanceController@addRecharge")->name('finance/addRecharge');
    Route::get("finance/log", "FinanceController@log")->name('finance/log');
    Route::get("finance/memberRecharge", "FinanceController@memberRecharge")->name('finance/memberRecharge');
    Route::get("finance/withdraw", "FinanceController@withdraw")->name('finance/withdraw');
	Route::get("finance/withdraw/create", "FinanceController@withdrawCreate")->name('finance/withdraw/create');
	Route::post("finance/withdraw/create", "FinanceController@withdrawCreate")->name('finance/withdraw/create');
    Route::post("finance/reject", "FinanceController@reject")->name('finance/reject');
    Route::post("finance/adopt", "FinanceController@adopt")->name('finance/adopt');
    Route::get("finance/finance_list", "FinanceController@finance_list")->name('finance/finance_list');
    Route::get("finance/fee", "FinanceController@fee")->name('finance/fee');
    Route::get("finance/finance_sum", "FinanceController@financeSum")->name('finance/finance_sum');
    Route::get("finance/payMing", "FinanceController@payMing")->name('finance/payMing');
    Route::get("finance/audit_list", "FinanceController@audit_list")->name('finance/audit_list');
    Route::post("finance/audit", "FinanceController@audit")->name('finance/audit');
    Route::post("finance/recharge_reject", "FinanceController@recharge_reject")->name('finance/recharge_reject');
	Route::get("finance/alilog", "FinanceController@alilog")->name('finance/alilog');

	Route::get("withdrawAudit", "WithdarwAuditController@index")->name('withdrawAudit');
	Route::get("withdrawAudit/add", "WithdarwAuditController@add")->name('withdrawAudit/add');
	Route::post("withdrawAudit/add", "WithdarwAuditController@add")->name('withdrawAudit/add');
	Route::post("withdrawAudit/audit", "WithdarwAuditController@audit")->name('withdrawAudit/audit');

	// 提货
	Route::get("delivery", "DeliveryController@index")->name('delivery');
	Route::post("delivery/audit/{id}", "DeliveryController@audit")->name('delivery/audit');
	Route::post("delivery/reject/{id}", "DeliveryController@reject")->name('delivery/reject');
	Route::post("delivery/note/{id}", "DeliveryController@note")->name('delivery/note');

    //公共路由
    Route::get('excel/projectOrderExport','ExcelController@projectOrderExport')->name('projectOrderExport');
    Route::get('excel/financeExport','ExcelController@financeExport')->name('financeExport');
    Route::get('excel/logExport','ExcelController@logExport')->name('logExport');
    Route::get('excel/withdrawExport','ExcelController@withdrawExport')->name('withdrawExport');
    Route::get('excel/auditExport','ExcelController@auditExport')->name('auditExport');
    Route::get('excel/feeExport','ExcelController@feeExport')->name('feeExport');
    Route::get('excel/btscoreExport','ExcelController@btscoreExport')->name('btscoreExport');
    Route::get('excel/blockTiquExport','ExcelController@blockTiquExport')->name('blockTiquExport');
    Route::get('excel/btshopDeliveryExport','ExcelController@btshopDeliveryExport')->name('btshopDeliveryExport');
    Route::get('excel/bankcardDrawExport','ExcelController@bankcardDrawExport')->name('bankcardDrawExport');
    Route::get('command/cache/clear', 'CommandController@cacheClear')->name('command.cache.clear');
    Route::get('command/trade/clear', 'CommandController@tradeClear')->name('command.trade.clear');

    Route::get('agent', 'AgentController@index')->name('agent');
	Route::get('agent/create', 'AgentController@create')->name('agent/create');
	Route::post('agent/create', 'AgentController@postCreate')->name('agent/postCreate');
	Route::get('agent/delete/{id}', 'AgentController@delete')->name('agent/delete');
	Route::get('agent/open/{id}', 'AgentController@open')->name('agent/open');
	Route::get('agent/detail/{phone}', 'AgentController@detail')->name('agent/detail');


	// 艺融宝
	Route::get('rong', 'RongController@index')->name('rong');
	Route::get('rong/userProduct', 'RongController@userProduct')->name('rong/userProduct');
	Route::get('rong/product/create', 'RongController@createProduct')->name('rong/product/create');
	Route::post('rong/product/create', 'RongController@postProduct')->name('rong/product/create');
	Route::post('rong/product/delete/{id}', 'RongController@delete')->name('rong/product/delete');
	Route::get('rong/product/edit/{id}', 'RongController@edit')->name('rong/product/edit');
	Route::post('rong/product/edit', 'RongController@postEdit')->name('rong/product/edit');
	Route::get('rong/endList', 'RongController@endList')->name('rong/endList');
	Route::post('rong/endAudit/{id}', 'RongController@endAudit')->name('rong/endAudit');

	Route::get('assets', 'AssetController@index')->name('assets');

	// 艺奖堂
	Route::get('tender', 'TenderController@index')->name('tender');
	Route::get('tender/withdraw', 'TenderController@withdraw')->name('tender/withdraw');
	Route::get('tender/flow', 'TenderController@flow')->name('tender/flow');
	Route::get('tender/guess', 'TenderController@guess')->name('tender/guess');
	Route::get('tender/tender', 'TenderController@tender')->name('admin/tender/tender');
	Route::get('tender/create', 'TenderController@create')->name('tender/create');
	Route::post('tender/create', 'TenderController@create')->name('tender/create');
	Route::post('tender/delete/{id}', 'TenderController@delete')->name('tender/delete');
	Route::get('tender/winners', 'TenderController@winners')->name('tender/winners');
	Route::get('tender/edit', 'TenderController@edit')->name('tender/edit');
	Route::post('tender/edit', 'TenderController@edit')->name('tender/edit');
	Route::post("tender/finish/{id}", "TenderController@finish")->name('tender/finish');
	// 管理员充值
	Route::get('tender/charge', 'TenderController@charge')->name('tender/charge');
	Route::post('tender/charge', 'TenderController@charge')->name('tender/charge');
	Route::post('tender/chargeReject', 'TenderController@chargeReject')->name('tender/chargeReject');
	Route::post('tender/chargeAccept/{id}', 'TenderController@chargeAccept')->name('tender/chargeAccept');

	// 提现审核
	Route::post("tender/adopt", "TenderController@adopt")->name('tender/adopt');
	Route::post("tender/reject", "TenderController@reject")->name('tender/reject');
	// 保证金
	Route::get('tender/margin', 'TenderController@margin')->name('tender/margin');
	Route::post('tender/marginDeduct/{id}', 'TenderController@marginDeduct')->name('tender/marginDeduct');
	// 用户反馈
	Route::get('tender/feedback', 'TenderController@feedback')->name('tender/feedback');
	Route::get('tender/courses', 'Tender\CourseController@index')->name('tender/courses');
	Route::get('tender/course/create', 'Tender\CourseController@create')->name('tender/course/create');
	Route::post('tender/course/create', 'Tender\CourseController@create')->name('tender/course/create');
	Route::get('tender/course/edit/{id}', 'Tender\CourseController@edit')->name('/admin/tender/course/edit');
	Route::post('tender/course/del/{id}', 'Tender\CourseController@del')->name('/admin/tender/course/del');

	// artbc
	Route::get('artbc/logs', 'ArtbcController@logs')->name('admin/artbc/logs');
	Route::get('artbc/ti', 'ArtbcController@ti')->name('admin/artbc/ti');
	Route::post('artbc/audit', 'ArtbcController@audit')->name('admin/artbc/audit');

	// artbc unlock
	Route::get('artbc/unlocks', 'ArtbcUnlockController@index')->name('admin/artbc/unlocks');
	Route::get('artbc/unlock/create', 'ArtbcUnlockController@create')->name('admin/artbc/unlock/create');
	Route::post('artbc/unlock/edit', 'ArtbcUnlockController@edit')->name('admin/artbc/unlock/edit');
	Route::post('artbc/unlock/del/{id}', 'ArtbcUnlockController@del')->name('admin/artbc/unlock/del');

	// 版通
    Route::get('btscore/logs', 'BtScoreController@index')->name('admin/btscore/logs');
    Route::get('btscore/unlock/logs', 'BtScoreUnlockController@index')->name('admin/btscore/unlock/logs');
    Route::get('btscore/unlock/create', 'BtScoreUnlockController@create')->name('admin/btscore/unlock/create');
    Route::post('btscore/unlock/edit', 'BtScoreUnlockController@unlockEdit')->name('admin/btscore/unlock/edit');
    Route::post('btscore/unlock/del/{id}', 'BtScoreUnlockController@del')->name('admin/btscore/unlock/del');
    Route::post('btscore/audit', 'BtScoreController@audit')->name('admin/btscore/audit');
    Route::get('btconfig/edit', 'BtConfigController@edit')->name('admin/btconfig/edit');
    Route::post('btconfig/edit', 'BtConfigController@edit')->name('admin/btconfig/edit');
    Route::get('btscore/sum', 'BtScoreController@sum')->name('admin/btscore/sum');

    // 兑换中心
    Route::get('btshop/products', 'BtshopProductController@index')->name('admin/btshop/products');
    Route::get('btshop/product/create', 'BtshopProductController@create')->name('admin/btshop/product/create');
    Route::post('btshop/product/create', 'BtshopProductController@create')->name('admin/btshop/product/create');
    Route::post('btshop/product/delete/{id}', 'BtshopProductController@delete')->name('admin/btshop/product/delete1');
    Route::post('btshop/product/enable', 'BtshopProductController@enable')->name('admin/btshop/product/enable');

    // 兑换中心-提货
    Route::get("btshop/delivery", "BtshopDeliveryController@index")->name('btshop/delivery');
    Route::post("btshop/delivery/audit/{id}", "BtshopDeliveryController@audit")->name('btshop/delivery/audit');
    Route::post("btshop/delivery/reject/{id}", "BtshopDeliveryController@reject")->name('btshop/delivery/reject');
    Route::post("btshop/delivery/note/{id}", "BtshopDeliveryController@note")->name('btshop/delivery/note');


    // 充值中心
    Route::get('block/recharges', 'BlockRechargeController@index')->name('admin/block/recharges');
    Route::get('block/asset/logs', 'BlockAssetController@index')->name('admin/block/asset/logs');
    Route::post('block/recharge/del/{id}', 'BlockRechargeController@del')->name('admin/block/recharge/del');
    Route::post('block/recharge/revise/{id}', 'BlockRechargeController@revise')->name('admin/block/recharge/revise');
    Route::get('block/tiqu', 'BlockAssetController@tiqu')->name('admin/block/asset/tiqu');
    Route::post('block/tiqu/audit/{id}', 'BlockAssetController@tiquAudit')->name('admin/block/tiqu/audit');
    Route::post('block/tiqu/reject', 'BlockAssetController@tiquReject')->name('admin/block/tiqu/reject');
    Route::post('block/recharge/tx/append', 'BlockRechargeController@txAppend')->name('admin/block/recharge/tx/append');

    Route::get('block/sale', 'BlockAssetController@sale')->name('admin/block/asset/sale');
    Route::post('block/sale/audit/{id}', 'BlockAssetController@saleAudit')->name('admin/block/sale/audit');

    Route::get('block/tibis', 'BlockAssetController@tibis')->name('admin/block/asset/tibis');
    Route::post('block/tibi/audit/{id}', 'BlockAssetController@tibiAudit')->name('admin/block/tibi/audit');
    Route::post('block/tibi/reject', 'BlockAssetController@tibiReject')->name('admin/block/tibi/reject');

    // 提现列表
    Route::get('alipay/draws', 'AlipayDrawController@index')->name('admin/alipay/draws');
    Route::get('bankcard/draws', 'BankcardController@index')->name('admin/bankcard/draws');
    Route::post('bankcard/draw/audit/{ids}', 'BankcardController@audit')->name('admin/bankcard/draw/audit');
    Route::post('bankcard/draw/reject', 'BankcardController@reject')->name('admin/bankcard/draw/reject');

    // 会员认证审核
    Route::get('profiles', 'ProfileController@index')->name('admin/profiles');
    Route::post('profile/audit/{id}', 'ProfileController@audit')->name('admin/profile/audit');
    Route::post('profile/reject', 'ProfileController@reject')->name('admin/profile/reject');
    Route::post('profile/revert', 'ProfileController@revert')->name('admin/profile/revert');

});
