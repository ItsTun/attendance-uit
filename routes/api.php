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

Route::post('/v1/attendance', 'AttendancesController@store');
Route::put('/v1/attendance', 'AttendancesController@update');
Route::get('/v1/attendance/{student}', 'AttendancesController@getStudentAttendance');
Route::get('/v1/attendance/{date}/student-list', 'AttendancesController@index');
Route::get('/v1/attendance/{roll_no}/detail', 'AttendancesController@detail');
Route::get('/v1/attendance/{roll_no}/totalabsence', 'AttendancesController@totalAbsence');
Route::get('/v1/attendance/{klass}/absentstudentlist', 'AttendancesController@absentStudentList');
Route::get('/v1/attendance/{student}/daily/detail', 'AttendancesController@dailyDetail');

Route::get('/v1/classes', 'KlassesController@index');

Route::get('/v1/period/{period}', 'PeriodsController@show');

Route::get('/v1/timetables/{class_id}', 'PeriodsController@timetable');

Route::get('/v1/students/{student}', 'StudentsController@show');
Route::get('/v1/students/email/{email}', 'StudentsController@findByEmail');

Route::get('/v1/logs', 'InternalLogController@index');