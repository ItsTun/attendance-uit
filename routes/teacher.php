<?php

Route::get('login', 'TeacherController@login');

Route::group(['middleware' => ['teacher', 'web']], function () {
	Route::get('timetable', 'TeacherController@timetable')->name('timetable');
	Route::get('students', 'TeacherController@studentAttendance')->name('students_attendance');
	Route::get('add/{period_id}', 'TeacherController@addAttendance');
	Route::post('add/{period_id}', 'TeacherController@saveOrEditAttendance');

	Route::get('attendance_details', 'TeacherController@attendanceDetails')->name('teacher.attendance_details');
	Route::get('getStudentDetails', 'TeacherController@getStudentAttendanceDetails')->name('teacher.getStudentAttendanceDetails');
	Route::get('getStudentAbsentList', 'TeacherController@getStudentsAbsentList')->name('teacher.getStudentsAbsentList');
	Route::get('absent_list', 'TeacherController@absentList')->name('teacher.absent_list');
	Route::get('logout', 'Auth\TeacherLoginController@logout');
});

Route::get('login/google', 'Auth\TeacherLoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\TeacherLoginController@handleProviderCallback');