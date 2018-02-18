<?php

/*
	Routes for /admin
*/


// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => ['admin', 'web']], function () {
    Route::get('dashboard', 'AdminController@dashboard')->name('admin-dashboard');
    Route::get('teachers', 'AdminController@teachers')->name('admin-teachers');
    Route::get('students', 'AdminController@students')->name('admin-students');
    Route::get('students/csv', 'AdminController@studentsCsv')->name('students.csv');
    Route::get('students/attendance_details', 'AdminController@studentsAttendanceDetails')->name('students.attendance_details');
    Route::get('students/absent_list', 'AdminController@studentsAbsentList')->name('students.absent_list');
    Route::get('teachers/csv', 'AdminController@teachersCsv')->name('teachers.csv');
    Route::get('classes', 'AdminController@classes')->name('admin-classes');
    Route::get('departments', 'AdminController@departments')->name('admin-departments');
    Route::get('subjects', 'AdminController@subjects')->name('admin-subjects');
    Route::get('admins', 'AdminController@admins')->name('admin-admins');
    Route::get('medical-leave', 'AdminController@medicalLeave')->name('admin-medical-leave');
    Route::get('timetables', 'AdminController@timetables')->name('admin-timetables');
    Route::get('attendance', 'AdminController@attendance')->name('admin-attendance');
    Route::get('attendance/percentage', 'AdminController@attendancePercentage')->name('admin.student.percentage');
    Route::get('years', 'AdminController@years')->name('admin-years');
    Route::get('getTeacherWithEmail', 'AdminController@getTeacherWithEmail')->name('admin.getTeacherWithEmail');
    Route::get('getStudentFromRollNo', 'AdminController@getStudentFromRollNo')->name('admin.getStudentFromRollNo');
    Route::post('saveMedicalLeave', 'AdminController@saveMedicalLeave')->name('admin.saveMedicalLeave');
    Route::post('suspendStudent', 'AdminController@suspendStudent')->name('admin.suspendStudent');
    Route::post('resumeStudent', 'AdminController@resumeStudent')->name('admin.resumeStudent');

    Route::get('getStudentIds', 'AdminController@getStudentIds')->name('admin.getStudentIds');
    Route::get('getStudentsFromClass', 'AdminController@getStudentsFromClass')->name('admin.getStudentsFromClass');
    Route::get('add/{period_id}', 'AdminController@addAttendance');
    Route::get('getStudent', 'AdminController@getStudent')->name('admin.getStudent');
    Route::get('getStudentWithEmail', 'AdminController@getStudentWithEmail');
    Route::get('checkIfEmailExists', 'AdminController@checkIfEmailExists')->name('admin.checkIfEmailExists');
    Route::get('checkIfUserEmailExists', 'AdminController@checkIfUserEmailExists')->name('admin.checkIfUserEmailExists');
    Route::get('checkIfRollNoExists', 'AdminController@checkIfRollNoExists')->name('admin.checkIfRollNoExists');
    Route::get('getStudentAttendanceDetails', 'AdminController@getStudentAttendanceDetails')->name('admin.getStudentAttendanceDetails');
    Route::get('getStudentsAbsentList', 'AdminController@getStudentsAbsentList')->name('admin.getStudentsAbsentList');
    Route::get('getStudentsAbsentForThreeDaysOrAbove', 'AdminController@getStudentsAbsentForThreeDaysOrAbove')->name('admin.getStudentsAbsentForThreeDaysOrAbove');

    Route::post('add/{period_id}', 'AdminController@saveOrEditAttendance');
    Route::post('addOrUpdatePeriods', 'AdminController@addOrUpdatePeriods')->name('admin.addOrUpdatePeriods');
    Route::post('addOrUpdateYear', 'AdminController@addOrUpdateYear');
    Route::post('addOrUpdateClass', 'AdminController@addOrUpdateClass');
    Route::post('addOrUpdateSubject', 'AdminController@addOrUpdateSubject');
    Route::post('students/getStudentArrayFromCSV', 'AdminController@getStudentArrayFromCSV')->name('admin.getStudentArrayFromCSV');
    Route::post('students/saveStudentsFromCSV', 'AdminController@saveStudentsFromCSV');
    Route::post('teachers/getTeacherArrayFromCSV', 'AdminController@getTeacherArrayFromCSV');
    Route::post('addOrUpdateStudent', 'AdminController@addOrUpdateStudent')->name('admin.addOrUpdateStudent');
    Route::post('addOrUpdateTeacher', 'AdminController@addOrUpdateTeacher');
    Route::post('addOrUpdateUser', 'AdminController@addOrUpdateUser');
    Route::post('addOrUpdateFaculty', 'AdminController@addOrUpdateFaculty');

    Route::get('migration-tool', 'AdminController@migrationTool');
    Route::post('migrateStudents', 'AdminController@migrateStudents')->name('admin.migrateStudents');
});

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
