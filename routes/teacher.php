<?php

Route::get('login', 'TeacherController@login');

Route::group(['middleware' => ['teacher']], function () {
	Route::get('timetable', 'TeacherController@timetable')->name('timetable');
	Route::get('students', 'TeacherController@studentAttendance')->name('students_attendance');
	Route::get('add/{period_id}', 'TeacherController@addAttendance');
	Route::get('logout', 'Auth\TeacherLoginController@logout');
});

Route::get('login/google', 'Auth\TeacherLoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\TeacherLoginController@handleProviderCallback');