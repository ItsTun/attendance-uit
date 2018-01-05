<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Config;

use App\Open_Period;
use App\Student;
use App\Period_Attendance;
use App\Period;
use App\Klass;
use App\Attendance;
use App\Utils;

class AttendancesController extends Controller
{
    public function store() {
    	$date = Input::get('date');
    	$presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        foreach($period_ids as $period_id) {
            $key = $period_id . '_student';
            $students = Input::post($key);
            $presentStudents[$key] = explode(',', $students);
        }

        Period_Attendance::saveAttendance($period_ids, $date, $presentStudents);

    	return response('Successfully added!');
    }

    public function update() {
        $date = Input::get('date');
        $presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        foreach ($period_ids as $period_id) {
            $key = $period_id . '_student';
            $students = Input::get($key);
            $presentStudents[$key] = explode(',', $students);
        }

        Period_Attendance::updateAttendance($period_ids, $date, $presentStudents);

        return response('Successfully updated!');
    }

    public function index(Request $request, $date) {
        $periodId = $request->period;

        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        $openPeriod = Open_Period::fetch($periodId, $date);

        // if attendance is already called
        if ($openPeriod) {
            $attendStudents = $openPeriod->attendedStudents()->orderByRaw('LENGTH(roll_no) ASC, roll_no ASC')->get();

            $responseAry = array();

            foreach ($attendStudents as $value) {
                $student = Student::find($value['roll_no']);

                $info['roll_no'] = $student->roll_no;
                $info['name'] = $student->name;
                $info['present'] = $value['present'];

                array_push($responseAry, $info);
            }

            return response($responseAry);
        } 
        // else
        $klassId = Period::find($periodId)->subject->class_id;

        $studentList = Student::where('class_id', $klassId)->orderByRaw('LENGTH(roll_no) ASC, roll_no ASC')->get();

        $responseAry = array();

        foreach ($studentList as $value) {
            $info['roll_no'] = $value->roll_no;
            $info['name'] = $value->name;
            $info['present'] = 0;

            array_push($responseAry, $info);
        }

        return response($responseAry);
    }

    public function detail(Request $request, $rollNo) {
        $month = $request->month;

        $date = strtotime($month);
        $month = date('m', $date);

        $results = Period_Attendance::getAttendanceDetail($rollNo, $month);

        if (!$results) return response('No data!');

        foreach ($results as $value) {
            $response[$value->date][$value->period_num]['subject'] = $value->subject_code;
            $response[$value->date][$value->period_num]['present'] = $value->present;
        }

        return response($response);
    }

    public function totalAbsence(Request $request, $rollNo) {
        $month = $request->month;

        $date = strtotime($month);
        $month = date('m', $date);

        $results = Period_Attendance::getTotalAbsence($rollNo, $month);

        if (!$results) return response('No data!');

        foreach ($results as $value) {
            
            $response[$value->subject_code] = $value->total_absence;

        }

        return response($response);
    }

    public function absentStudentList(Request $request, $klass) {
        $month = $request->month;

        $date = strtotime($month);
        $month = date('m', $date);

        $results = Period_Attendance::getAbsentStudentList($klass, $month);

        if (!$results) return response('No data!');

        $response = [];

        if (count($results) > 0)
            $previousStudent = $results[0]->roll_no;

        foreach ($results as $value) {

            if ($value->roll_no != $previousStudent) {

                array_push($response, $info);

                $previousStudent = $value->roll_no;

                $info = null;

            }

            $info['student']['roll_no'] = $value->roll_no;
            $info['student']['name'] = $value->name;
            $info['total_absence'][$value->subject_code] = $value->total_absence;

        }

        array_push($response, $info);

        return response($response);
    }

    public function dailyDetail(Request $request, Student $student) {
        $date = $request->date;

        $date = Utils::getDate($date);

        $results = Period_Attendance::getDailyDetail($student, $date);
        if (!$results) return response('No data!');

        $attendance = [];
        foreach ($results as $value) {
            $attendance[$value->period_id] = $value->present;
        }

        $day = Utils::getDayFromDate($date);
        $timetable = Period::getTimetable($student->class_id, $day);
        if (!$timetable) return response('No data!');

        $response = [];

        foreach ($timetable as $key => $value) {
            $info['period_id'] = $value->period_id;
            $info['subject_code'] = $value->subject_code;
            $info['subject_name'] = $value->subject_name;
            $info['subject_class_id'] = $value->subject_class_id;
            $info['period_num'] = $value->period_num;
            $info['room'] = $value->room;
            $info['start_time'] = $value->start_time;
            $info['end_time'] = $value->end_time;
            $info['day'] = $day;
            $info['class_short_form'] = $value->class_short_form;
            $info['class_name'] = $value->class_name;

            if (array_key_exists($value->period_id, $attendance)) {
                $info['present'] = $attendance[$value->period_id];
            } else {
                $info['present'] = -1;
            }

            array_push($response, $info);
        }

        return response($response);
    }

    public function getStudentAttendance(Student $student) {
        return response(Attendance::show($student->roll_no)->attendance_json);
    }
}
