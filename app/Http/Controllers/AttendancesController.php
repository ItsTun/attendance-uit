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

class AttendancesController extends Controller
{
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

    public function studentAttendance(Request $request) {
        $student_id = $request->student_id;
        $studentAttendance = json_decode(Attendance::show($student_id)->attendance_json);
        return response($studentAttendance);
    }

    public function attendanceDetails(Student $student, $subject_class_id) {
        $studentAttendance = json_decode(Attendance::show($student->roll_no)->attendance_json);
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

        $attendance = Period_Attendance::getMonthlyAttendance($student->roll_no, $subject_class_id);
        $_studentAttendance->attendance = $attendance->toArray();
        
        return response(json_encode($_studentAttendance));
    }
}
