<?php

namespace App\Http\Controllers;

use App\Period;
use App\Teacher;
use App\Student;
use App\Utils;
use App\Period_Attendance;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class TeacherController extends Controller
{
    public function login() {
        if(Auth::check() && Auth::user()->isAdmin()){
            Auth::logout();
        }
        return view('teacher.login');
    }

    public function timetable() {
    	$period = new Period();
        $date = Input::get('date');
        $msgCode = Input::get('msg_code');

        if(!is_null($date) && !Utils::validateDate($date)) {
            return "Invalid date format!";
        }

    	$teacher_id = Teacher::where('email', Auth::user()->email)->first()->teacher_id;
    	$timetable = $period->getTeacherTimetable($teacher_id, (!is_null($date)) ? Utils::getDayFromDate($date) : date('N'));

        $with = ['timetables' => $timetable, 'dates' => Utils::getDatesInThisWeek()];
        $with['selectedDate'] = (!is_null($date)) ? $date : date("Y-m-d");
        $with['msgCode'] = (!is_null($msgCode)) ? $msgCode : 0;

        return view('teacher.timetable')->with($with);
    }

    public function addAttendance($period_id) {
        $date = Input::get('date');
        if(Utils::validateDate($date)) {
            if(Utils::checkDateIsEligible($date)){
                if(Utils::periodIsInDate($period_id, $date)) {
                    $students = Student::getStudentFromPeriod($period_id);
                	return view('teacher.add_attendance')->with(['students'=>$students,'period'=>$period_id, 'date'=>$date]);
                } else {
                    return "There is no period with id $period_id in $date";
                }
            } else {
                return "You can't add attendance for $date. It is either because the date is ahead of current time or the period to add this attendance has expired.";
            }
        } else {
            return "Invalid date format!";
        }
    }

    public function saveAttendance() {
        $date = Input::get('date');
        $presentStudents = Input::get('student');
        $periods = Input::get('period');

        Period_Attendance::saveAttendance($periods, $date, $presentStudents);

        return redirect()->action('TeacherController@timetable', ['msg_code' => '1']);
    }
}
