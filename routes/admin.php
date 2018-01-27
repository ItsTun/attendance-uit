<?php

/*
	Routes for /admin
*/


// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Route::group(['middleware' => ['admin', 'web']], function () {
	Route::get('dashboard', 'AdminController@dashboard')->name('admin-dashboard');
	Route::get('teachers', 'AdminController@teachers')->name('admin-teachers');
	Route::get('students', 'AdminController@students')->name('admin-students');
	Route::get('students/csv', 'AdminController@studentsCsv')->name('students.csv');
	Route::get('students/attendance_details', 'AdminController@studentsAttendanceDetails')->name('students.attendance_details');
	Route::get('students/absent_list', 'AdminController@studentsAbsentList')->name('students.absent_list');
	Route::get('teachers/csv', 'AdminController@teachersCsv')->name('teachers.csv');
	Route::get('classes', 'AdminController@classes')->name('admin-classes');
	Route::get('subjects', 'AdminController@subjects')->name('admin-subjects');
	Route::get('add_new_admin', 'AdminController@addNewAdmin');
	Route::get('timetables', 'AdminController@timetables')->name('admin-timetables');
	Route::get('attendance', 'AdminController@attendance')->name('admin-attendance');
	Route::get('years', 'AdminController@years')->name('admin-years');
	Route::get('getTeacherWithEmail', 'AdminController@getTeacherWithEmail');
	Route::get('students/batch_update', 'AdminController@batchUpdate');
	Route::get('add/{period_id}', 'AdminController@addAttendance');
	Route::get('getStudent', 'AdminController@getStudent');
	Route::get('getStudentWithEmail', 'AdminController@getStudentWithEmail');
	Route::get('checkIfEmailExists', 'AdminController@checkIfEmailExists');
	Route::get('checkIfRollNoExists', 'AdminController@checkIfRollNoExists');
	Route::get('getStudentAttendanceDetails', 'AdminController@getStudentAttendanceDetails');
	Route::get('getStudentsAbsentList', 'AdminController@getStudentsAbsentList');

	Route::post('add/{period_id}', 'AdminController@saveOrEditAttendance');
	Route::post('addOrUpdatePeriods', 'AdminController@addOrUpdatePeriods');
	Route::post('addOrUpdateYear', 'AdminController@addOrUpdateYear');
	Route::post('addOrUpdateClass', 'AdminController@addOrUpdateClass');
	Route::post('addOrUpdateSubject', 'AdminController@addOrUpdateSubject');
	Route::post('students/getStudentArrayFromCSV', 'AdminController@getStudentArrayFromCSV');
	Route::post('students/saveStudentsFromCSV', 'AdminController@saveStudentsFromCSV');
	Route::post('teachers/getTeacherArrayFromCSV', 'AdminController@getTeacherArrayFromCSV');
	Route::post('addOrUpdateStudent', 'AdminController@addOrUpdateStudent');
	Route::post('addOrUpdateTeacher', 'AdminController@addOrUpdateTeacher');
// });

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
