<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Config;

use App\Open_Period;
use App\Student;
use App\Period_Attendance;
use App\Period;

class AttendanceController extends Controller
{
    public function store() {

    	$date = Input::get('date');
    	$periodId = Input::get('period');
    	$presentStudents = Input::get('present');
    	$klass = Input::get('class');

    	$presentStudentsAry = explode(',', $presentStudents);

    	$date = strtotime($date);
    	$date = date('Y-m-d', $date);

    	$openPeriod = Open_Period::firstOrNew(array('date' => $date, 'period_id' => $periodId));
    	$openPeriod->save();

    	$openPeriodId = $openPeriod->open_period_id;

    	$students = Student::select('roll_no')->where('class_id', $klass)->get();

    	foreach ($students as $value) {

    		$rollNo = $value['roll_no'];

    		$periodAttendance = new Period_Attendance();
    		$periodAttendance->roll_no = $rollNo;
    		$periodAttendance->open_period_id = $openPeriodId;
    		$periodAttendance->present = in_array($rollNo, $presentStudentsAry);
    		$periodAttendance->save();

    	}

    	return response('Successfully added!');

    }

    public function update() {

        $date = Input::get('date');
        $periodId = Input::get('period');
        $presentStudents = Input::get('present');

        $presentStudentsAry = explode(',', $presentStudents);

        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        $openPeriod = Open_Period::where('date', $date)
                                ->where('period_id', $periodId)
                                ->first();

        foreach ($openPeriod->attendStudents as $periodAttendance) {

            $rollNo = $periodAttendance['roll_no'];
            
            $periodAttendance->present = in_array($rollNo, $presentStudentsAry);
            $periodAttendance->save();

        }

        return response('Successfully updated!');

    }

    public function index(Request $request, $date) {

        $periodId = $request->period;

        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        $openPeriod = Open_Period::where('period_id', $periodId)
                                ->where('date', $date)
                                ->first();

        // if attendance is already called
        if ($openPeriod) {

            $responseAry = array();

            foreach ($openPeriod->attendStudents as $value) {

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

        $studentList = Student::where('class_id', $klassId)->get();

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

        $results = DB::select( DB::raw(
            'SELECT open_periods.open_period_id, open_periods.date, periods.period_num, subjects.subject_code, period_attendance.present 
            FROM open_periods, students, subjects, periods, period_attendance 
            WHERE MONTH(open_periods.date) = :month
                AND students.roll_no = :roll_no 
                AND subjects.class_id = students.class_id 
                AND periods.subject_id = subjects.subject_id 
                AND open_periods.period_id = periods.period_id 
                AND period_attendance.roll_no = students.roll_no 
                AND period_attendance.open_period_id = open_periods.open_period_id
            ORDER BY open_periods.open_period_id, periods.period_num;'
        ), array('month' => $month, 'roll_no' => $rollNo) );

        foreach ($results as $value) {
            
            $response[$value->date][$value->period_num]['subject'] = $value->subject_code;
            $response[$value->date][$value->period_num]['present'] = $value->present;

        }

        return response($response);

    }

    public function totalAbsence(Request $request, $rollNo) {

        

    }

}
