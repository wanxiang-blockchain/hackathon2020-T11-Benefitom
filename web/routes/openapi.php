<?php
/**
 * for open platform
 */
Route::group(['middleware' => ['record.query']], function() {
    Route::post('/sms', 'SmsController@index')->name('openapi.sms');
    // 开放平台，登录注册合一接口
    Route::post('/sms/verify', 'SmsController@verify')->name('openapi.sms.verify');
});