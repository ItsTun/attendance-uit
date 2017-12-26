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

    private function check($date, $periods) {
        if(Utils::validateDate($date)) {
            if(Period::checkIfPeriodsAreTaughtByCurrentTeacher($periods)){
                if(Utils::checkDateIsEligible($date)){
                    foreach($periods as $period) {
                        if(Utils::periodIsInDate($periods, $date)) {
                            return null;
                        } else {
                            return "There is no period with id $period in $date";
                        }
                    }
                } else {
                    return "You can't add attendance for $date. It is either because the date is ahead of current time or the period to add this attendance has expired.";
                }
            } else {
                return "You can only add attendance for periods you teach.";
            }
        } else {
            return "Invalid date format!";
        }
    }

    public function addAttendance($period_ids) {
        $date = Input::get('date');
        $periods = explode(',', $period_ids);
        $error = $this->check($date, $periods);
        $periodObjects = Period::find($periods);
        $numberOfPeriods = Period::getUniquePeriodNumber($periods);
        if(is_null($error)) {
            $students = Student::getStudentsFromPeriod($periods);
        	return view('teacher.add_attendance')->with(['students'=>$students,'periods'=> $periods, 'date'=>$date, 
                'periodObjects' => $periodObjects, 'numberOfPeriods'=>$numberOfPeriods]);
        } else {
            return $error;
        }
    }

    public function updateAttendance() {
        $date = Input::get('date');
        $presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        foreach($period_ids as $period_id) {
            $key = $period_id . '_student';
            $students = Input::post($key);
            $presentStudents[$key] = (is_null($students))?[]:$students;
        }

        Period_Attendance::updateAttendance($period_ids, $date, $presentStudents);

        return redirect()->action('TeacherController@timetable', ['msg_code' => '2']);
    }

    public function saveAttendance() {
        $date = Input::get('date');
        $presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        foreach($period_ids as $period_id) {
            $key = $period_id . '_student';
            $students = Input::post($key);
            $presentStudents[$key] = (is_null($students))?[]:$students;
        }

        Period_Attendance::saveAttendance($period_ids, $date, $presentStudents);

        return redirect()->action('TeacherController@timetable', ['msg_code' => '1']);
    }
}
