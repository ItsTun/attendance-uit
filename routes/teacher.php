<?php

Route::get('login', 'TeacherController@login');

Route::group(['middleware' => ['teacher']], function () {
	Route::get('timetable', 'TeacherController@timetable');
	Route::get('logout', 'Auth\LoginController@logout')->name('logout');
});

Route::get('login/google', 'Auth\LoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');