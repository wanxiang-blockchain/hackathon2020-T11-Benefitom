<?php
/**
 * Created by PhpStorm.
 * User: johnShaw
 * Date: 17/8/27
 * Time: 下午3:19
 */

Route::group(['middleware' => ['sso', 'member:front', 'filters']], function () {
	Route::get('', 'RongController@index')->name('rong');
	Route::get('/index', 'RongController@index')->name('index');
	Route::get('/buy/{id}', 'RongController@buy')->name('buy');
	Route::post('/buy', 'RongController@postBuy')->name('buy');
	Route::get('/detail/{id}', 'RongController@detail')->name('rong/detail');
	Route::get('/protocol', 'RongController@protocol')->name('rong/protocol');
});
