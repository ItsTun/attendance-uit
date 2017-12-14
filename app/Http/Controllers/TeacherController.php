<?php

namespace App\Http\Controllers;

use App\Period;
use App\Teacher;

use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function login() {
        return view('teacher.login');
    }

    public function timetable() {
    	$period = new Period();
    	$teacher_id = Teacher::where('email', Auth::user()->email)->first()->teacher_id;
    	//$timetable = $period->getTeacherTimetable($teacher_id, date('N'));
    	$timetable = $period->getTeacherTimetable($teacher_id, 1);
        return view('teacher.timetable')->with('timetables', $timetable);
    }
}
