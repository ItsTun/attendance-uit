<?php

Route::get('login', 'TeacherController@login');

Route::group(['middleware' => ['auth', 'teacher']], function () {
	Route::get('dashboard', 'TeacherController@dashboard');
	Route::get('logout', 'Auth\LoginController@logout')->name('logout');
});

Route::get('login/google', 'Auth\LoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');