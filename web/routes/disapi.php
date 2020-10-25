<?php


Route::post('/members', 'MemberController@members')->name('disapi.members');
Route::post('/member', 'MemberController@member')->name('disapi.member');
Route::post('/member/verify', 'MemberController@verify')->name('disapi.member.verify');
Route::post('/arttbc/price', 'ArtbcController@price')->name('disapi.artbc.price');
Route::post('/score/list', 'ArtbcController@scores')->name('disapi.scores');

Route::group(['middleware' => ['disverify', 'record.query']], function(){
    // 积分兑换 ARTTBC
    Route::post('/score/draw', 'ArtbcController@draw')->name('disapi.score.draw');
});
