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

    public function addAttendance($period_ids) {
        $date = Input::get('date');
        $periods = explode(',', $period_ids);
        if(Utils::validateDate($date)) {
            if(Period::checkPeriodsAreOfSameClassAndSubject($periods)){
                if(Utils::checkDateIsEligible($date)){
                    if(Utils::periodIsInDate($period_ids, $date)) {
                        $students = Student::getStudentFromPeriod($period_ids);
                    	return view('teacher.add_attendance')->with(['students'=>$students,'period'=>$period_ids, 'date'=>$date, 
                            'period_ids' => $periods]);
                    } else {
                        return "There is no period with id $period_ids in $date";
                    }
                } else {
                    return "You can't add attendance for $date. It is either because the date is ahead of current time or the period to add this attendance has expired.";
                }
            } else {
                return "Only periods of same class and subjects are allowed.";
            }
        } else {
            return "Invalid date format!";
        }
    }

    public function saveAttendance() {
        $date = Input::get('date');
        $presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        foreach($period_ids as $period_id) {
            $key = $period_id . '_student';
            $presentStudents[$key] = Input::post($key);
        }

        Period_Attendance::saveAttendance($period_ids, $date, $presentStudents);

        return redirect()->action('TeacherController@timetable', ['msg_code' => '1']);
    }
}
