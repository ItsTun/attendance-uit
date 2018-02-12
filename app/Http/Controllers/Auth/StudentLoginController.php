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

        if(!is_null($socialUser)) {

            $email = $socialUser->email;

            $student = Student::where('email', $email)->whereNull('suspended')->first();

            if (is_null($student)) {
                return response(['error' => 'Student not found', 'message' => 'No student with email address, ' . $email], 404)
                    ->header('Content-Type', 'application/json');
            }

            $student['klass']->year;
            $student['class'] = $student->klass;

            $klass = $student['klass'];

            unset($student['class_id']);
            unset($student['klass']);
            unset($student['suspended']);
            unset($student['suspended_at']);
            unset($student['created_at']);
            unset($student['updated_at']);
            unset($klass['created_at']);
            unset($klass['updated_at']);
            unset($klass['year_id']);

            $year = $klass->year;
            unset($year['created_at']);
            unset($year['updated_at']);


            return response(['student' => $student], 200)
                ->header('Content-Type', 'application/json');
        } else {
            return response(['error' => 'Invalid token', 'message' => 'The provided token is invalid'], 401)
                ->header('Content-Type', 'application/json');
        }
    }
}