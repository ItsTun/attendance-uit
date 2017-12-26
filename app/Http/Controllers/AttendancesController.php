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
        $presentStudents = Input::get('student');
        $periods = Input::get('period');

        $periodAry = explode(',', $periods);

        $presentStudentsAry = explode(',', $presentStudents);

        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        foreach ($periodAry as $periodId) {    
            $openPeriod = Open_Period::where('date', $date)
                        ->where('period_id', $periodId)
                        ->first();

            foreach ($openPeriod->attendStudents as $periodAttendance) {

                $rollNo = $periodAttendance['roll_no'];
                
                $periodAttendance->present = in_array($rollNo, $presentStudentsAry);
                $periodAttendance->save();

            }
        }

        return response('Successfully updated!');
    }

    public function index(Request $request, $date) {
        $periodId = $request->period;

        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        $openPeriod = Open_Period::fetch($periodId, $date);

        // if attendance is already called
        if ($openPeriod) {
            $attendStudents = $openPeriod->attendStudents()->orderByRaw('LENGTH(roll_no) ASC, roll_no ASC')->get();

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

        $date = strtotime($date);
        $dayOfWeek = date('w', $date);
        $month = date('m', $date);
        $date = date('Y-m-d', $date);

        $results = Period_Attendance::getDailyDetail($student, $date, $dayOfWeek);

        if (!$results) return response('No data!');

        $totalAbsenceResults = Period_Attendance::getTotalAbsence($student->roll_no, $month);

        if (!$totalAbsenceResults) return response('No data!');

        foreach ($totalAbsenceResults as $value) {
            $totalAbsence[$value->subject_code] = $value->total_absence;
        }

        foreach ($results as $key => $value) {
            $info['period_id'] = $value->period_id;
            $info['subject_code'] = $value->subject_code;
            $info['subject_name'] = $value->name;
            $info['present'] = $value->present;
            $info['total_absence'] = $totalAbsence[$value->subject_code];

            $response[$value->period_num] = $info;
        }

        $periodResults = Period::getTimetable($student->class_id, $dayOfWeek);

        if (!$periodResults) return response('No data!');

        foreach ($periodResults as $value) {

            $response[$value->period_num]['subject_code'] = $value->subject_code;
            $response[$value->period_num]['duration'] = $value->duration;
            $response[$value->period_num]['room'] = $value->room;
            $response[$value->period_num]['teacher_name'] = $value->teacher_name;

        }

        return response($response);
    }

}
