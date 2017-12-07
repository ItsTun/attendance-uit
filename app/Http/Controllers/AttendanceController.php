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

        $month = $request->month;

        $date = strtotime($month);
        $month = date('m', $date);

        $results = DB::select( DB::raw(
            'SELECT subjects.subject_code, count(open_periods.open_period_id) - sum(period_attendance.present) as total_absence 
            FROM subjects, periods, open_periods, period_attendance, students
            WHERE periods.subject_id = subjects.subject_id 
            AND open_periods.period_id = periods.period_id 
            AND period_attendance.open_period_id = open_periods.open_period_id 
            AND students.roll_no = :roll_no 
            AND period_attendance.roll_no = students.roll_no 
            AND subjects.class_id = students.class_id 
            AND MONTH(open_periods.date) = :month 
            GROUP BY subjects.subject_code;'
        ), array('roll_no' => $rollNo, 'month' => $month) );

        if (!$results) return response('No Data!');

        foreach ($results as $value) {
            
            $response[$value->subject_code] = $value->total_absence;

        }

        return response($response);

    }

    public function absentStudentList(Request $request, $klass) {

        $month = $request->month;

        $date = strtotime($month);
        $month = date('m', $date);

        $results = DB::select( DB::raw(
            'SELECT students.roll_no, students.name,subjects.subject_code, count(open_periods.open_period_id) - sum(period_attendance.present) as total_absence 
            FROM subjects, periods, open_periods, period_attendance, students
            WHERE periods.subject_id = subjects.subject_id 
            AND open_periods.period_id = periods.period_id 
            AND period_attendance.open_period_id = open_periods.open_period_id 
            AND period_attendance.roll_no = students.roll_no 
            AND subjects.class_id = :klass
            AND MONTH(open_periods.date) = :month 
            GROUP BY students.roll_no, subjects.subject_code;'
        ), array('klass' => $klass, 'month' => $month) );

        if (!$results) return response('No Data!');

        $response = [];

        if (count($results) > 0)
            $previousStudent = $results[0]->roll_no;

        foreach ($results as $value) {

            if ($value->roll_no != $previousStudent) {

                array_push($response, $info);

                $previousStudent = $value->roll_no;

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
        $date = date('Y-m-d', $date);

        $results = DB::select( DB::raw(
            'SELECT B.period_id, B.subject_code, B.period_num, A.present 
            FROM 
            ( SELECT periods.period_id, subjects.subject_code, periods.period_num, period_attendance.present
            FROM periods, subjects, open_periods, period_attendance, students 
            WHERE subjects.class_id = students.class_id 
            AND periods.subject_id = subjects.subject_id 
            AND open_periods.date = :date 
            AND period_attendance.open_period_id = open_periods.open_period_id 
            AND students.roll_no = :roll_no 
            AND period_attendance.roll_no = students.roll_no 
            AND open_periods.period_id = periods.period_id
            ORDER BY periods.period_num ) A 
            RIGHT OUTER JOIN 
            ( SELECT periods.period_id, subjects.subject_code, periods.period_num 
            FROM periods, subjects 
            WHERE subjects.class_id = :klass 
            AND periods.subject_id = subjects.subject_id 
            AND periods.day = :day ) B 
            ON A.period_id = B.period_id
            ORDER BY B.period_num;'
        ), array('roll_no' => $student->roll_no, 'date' => $date, 'klass' => $student->class_id, 'day' => $dayOfWeek) );

        return response($results);

    }

}
