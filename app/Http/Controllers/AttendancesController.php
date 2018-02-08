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
use App\Teacher;
use App\Subject_Class;

class AttendancesController extends Controller
{
    public function dailyDetail(Request $request) {
        $student_id = $request->student_id;
        $date = $request->date;

        $student = Student::find($student_id);
        if ($student == null) {
            return response('Student not found!', 200);
        }

        $date = Utils::getDate($date);

        $results = Period_Attendance::getDailyDetail($student, $date);
        if (!$results) {
            return response('No data!', 200);
        }

        $attendance = [];
        foreach ($results as $value) {
            $attendance[$value->period_num] = $value->present;
        }

        $day = Utils::getDayFromDate($date);
        $timetable = Period::getTimetable($day, $student->class_id);
        if (!$timetable) return response('No data!', 200);

        $new_timetable = [];
        foreach ($timetable as $period) {
            $period_num = $period->period_num;
            if (!array_key_exists($period_num, $new_timetable)) {
                $new_timetable[$period_num] = [];
            }
            array_push($new_timetable[$period_num], $period);
        }

        $response = [];
        $free_period = Subject_Class::getFreeSubjectClass($student->class_id);
        $lunch_period = Subject_Class::getLunchBreakSubjectClassId($student->class_id);
        foreach ($new_timetable as $period_ary) {
            $period = Utils::getAssociatedPeriod($period_ary, $date);
            if (!array_key_exists($period->period_num, $attendance)) {
                $info['period_id'] = $period->period_id;
                $info['subject_class_id'] = $period->subject_class_id;
                $info['period_num'] = $period->period_num;
                $info['start_time'] = $period->start_time;
                $info['end_time'] = $period->end_time;
                $info['day'] = $day;
                $info['present'] = -1;
                $info['date'] = $date;
                if ($period->subject_class_id == $free_period->subject_class_id) {
                    $info['subject_code'] = 'Free';
                    $info['subject_name'] = '';
                    $info['room'] = '';
                } else if ($period->subject_class_id == $lunch_period->subject_class_id) {
                    continue;
                } else {
                    $info['subject_code'] = $period->subject_class->subject->subject_code;
                    $info['subject_name'] = $period->subject_class->subject->name;
                    $info['room'] = $period->room;
                }
            } else {
                $info['period_id'] = $period->period_id;
                $info['subject_code'] = $period->subject_class->subject->subject_code;
                $info['subject_name'] = $period->subject_class->subject->name;
                $info['subject_class_id'] = $period->subject_class_id;
                $info['period_num'] = $period->period_num;
                $info['room'] = $period->room;
                $info['start_time'] = $period->start_time;
                $info['end_time'] = $period->end_time;
                $info['day'] = $day;
                $info['present'] = $attendance[$period->period_num];
                $info['date'] = $date;
            }
            array_push($response, $info);
        }
        return response($response, 200);
    }

    public function studentAttendance(Request $request) {
        $student_id = $request->student_id;
        $attendance = Attendance::show($student_id);
        if ($attendance == null) {
            return response('Student not found!', 200);
        }
        $studentAttendance = json_decode($attendance->attendance_json);
        return response($studentAttendance, 200);
    }

    public function attendanceDetails(Request $request) {
        $student_id = $request->student_id;
        $student = Student::find($student_id);
        if ($student == null) {
            return response('Student not found!', 200);
        }
        $subject_class_id = $request->subject_class_id;
        $studentAttendance = json_decode(Attendance::show($student->student_id)->attendance_json);
        $_studentAttendance = [];
        foreach ($studentAttendance as $key => $value) {
            if ($studentAttendance[$key]->subject_class_id == $subject_class_id) {
                $_studentAttendance = $value;
                break;
            }
        }

        $teachers = Teacher::getTeachersBySubjectClass($_studentAttendance->subject_class_id);
        $teachers_ary = [];
        foreach ($teachers as $value) {
            array_push($teachers_ary, $value->name);
        }
        $_studentAttendance->teachers = implode(', ', $teachers_ary);

        $attendance = Period_Attendance::getMonthlyAttendance($student->student_id, $subject_class_id);
        $_studentAttendance->attendance = $attendance->toArray();
        
        return response(json_encode($_studentAttendance), 200);
    }
}
