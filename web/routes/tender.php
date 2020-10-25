<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/10/14
 * Time: 上午10:06
 */
Route::group(['middleware' => ['sso', 'filters']], function () {
	Route::group(['middleware' => ['member:front']], function(){
		Route::get('/my', 'TenderController@my')->name('my');
		Route::get('/myauction', 'TenderController@myauction')->name('myauction');
		Route::get('/myauctions/{lastId}', 'TenderController@myauctions')->name('myauctions');

		// 保证金
		Route::get('/margin', 'TenderController@margin')->name('margin');

		// 充值
		Route::get('/recharge', 'TenderController@recharge')->name('recharge');


		// 我的估价
		Route::get('/myguess', 'TenderController@myguess')->name('myguess');
		Route::get('/myguesses/{lastId}', 'TenderController@myguesses')->name('myguesses');

		// 我的流水
		Route::get('/mybill', 'TenderController@mybill')->name('bill');
		Route::get('/mybills/{lastId}', 'TenderController@mybills')->name('bills');

		// 提现
		Route::get('/withdraw', 'TenderController@withdraw')->name('withdraw');

		// 估价或竞拍
		Route::post('/guess', 'TenderController@guess')->name('guess');
		Route::post('/tender', 'TenderController@tender')->name('tender/tender');
		Route::post('/margin', 'TenderController@margin')->name('margin');
		Route::post('/prepay', 'TenderController@prepay')->name('prepay');
		Route::post('/withdraw', 'TenderController@withdraw')->name('withdraw');

		// 合同
		Route::get('/contract', 'ContractController@index')->name('contract');
		Route::post('/contract/{flag}', 'ContractController@agree')->name('agree');

		// 打卡
		Route::get('/hasSigned', 'SignupController@hasSigned')->name('hasSigned');
		Route::post('/signup', 'SignupController@signup')->name('signup');

		Route::get('/logout', 'TenderController@logout')->name('/tender/logout');

		Route::get('/unReadMsgCount', 'TenderController@unReadMsgCount')->name('/tender/unReadMsgCount');

		Route::get('/mymsgs', 'TenderController@mymsgs')->name('/tender/mymsgs');
		Route::post('/msgRead/{id}', 'TenderController@msgRead')->name('/tender/msgRead');
		Route::get('/feedback', 'TenderController@feedback')->name('/tender/feedback');
		Route::post('/feedback', 'TenderController@feedback')->name('/tender/feedback');

	});

	// 主页不登录也可看
	Route::get('/', 'TenderController@index')->name('tender');
	Route::get('/index', 'TenderController@index')->name('index');
	Route::get('/detail/{id}', 'TenderController@detail')->name('id');
	Route::get('/guessed/{lastId}', 'TenderController@finished')->name('guessed');
	Route::get('/finished/{lastId}', 'TenderController@finished')->name('finished');

	// 关于
	Route::get('/about', 'TenderController@about')->name('about');

	Route::get('/rule', 'TenderController@rule')->name('/tender/rule');
	Route::get('/aboutMe', 'TenderController@aboutMe')->name('/tender/aboutMe');
	Route::get('/fqa', 'TenderController@fqa')->name('/tender/fqa');

	// 课堂
	Route::get('/course', 'TenderController@course')->name('/tender/course');
	Route::get('/course/detail/{id}', 'TenderController@courseDetail')->name('/tender/course/detail');

});

Route::post('/paycallback', 'PayController@callback')->name('paycallback');
//Route::get('/paycallback', 'PayController@callback')->name('paycallback');
