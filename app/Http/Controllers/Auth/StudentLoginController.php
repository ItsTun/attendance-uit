<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use App\OAuthClient;
use App\Student;


class StudentLoginController extends Controller
{
	public function getStudentRecord() {
		$token = Input::post('id_token');

		$driver = Socialite::driver('google');
		$socialUser = $driver->userFromToken($token);

		$email = $socialUser->email;

		$student = Student::where('email', $email)->first();
		
		if (is_null($student)) {
			 return response(['error_message' => 'No student with this email address, '. $email], 404)
                  ->header('Content-Type', 'application/json');
		}
		
		$student['klass']->year;
		$student['class'] = $student->klass;

		$klass = $student['klass'];

		unset($student['class_id']);
		unset($student['klass']);
		unset($klass['year_id']);

		return response(['student' => $student], 200)
                  ->header('Content-Type', 'application/json');
	}
}