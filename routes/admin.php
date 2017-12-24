<?php

/*
	Routes for /admin
*/


// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => ['admin']], function () {
	Route::get('dashboard', 'AdminController@dashboard');
	Route::get('teachers', 'AdminController@teachers');
	Route::get('students', 'AdminController@students');
	Route::get('classes', 'AdminController@classes');
	Route::get('subjects', 'AdminController@subjects');
	Route::get('add_new_admin', 'AdminController@addNewAdmin');
	Route::get('timetables', 'AdminController@timetables');
	Route::get('attendance', 'AdminController@attendance');
	Route::get('years', 'AdminController@years');
});

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');