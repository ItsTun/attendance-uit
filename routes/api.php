<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/auth/google/tokensignin', 'Auth\StudentLoginController@getStudentRecord');

// Route::group(['middleware' => ['client']], function () {
	Route::get('/v1/attendance/{student_id}', 'AttendancesController@studentAttendance'); // using
	Route::get('/v1/attendance/{student}/daily/detail', 'AttendancesController@dailyDetail'); // using
	Route::get('/v1/attendance/details/{student}/{subject_class_id}', 'AttendancesController@attendanceDetails'); // using
	Route::get('/v1/students/email/{email}', 'StudentsController@findByEmail'); // using
// });