<?php

Route::get('login', 'TeacherController@login');

Route::group(['middleware' => ['teacher']], function () {
	Route::get('timetable', 'TeacherController@timetable');
	Route::get('add', 'TeacherController@addAttendance');
	Route::get('logout', 'Auth\TeacherLoginController@logout');
});

Route::get('login/google', 'Auth\TeacherLoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\TeacherLoginController@handleProviderCallback');