<?php

namespace App\Http\Controllers;

use App\Period;
use App\Teacher;
use App\Student;
use App\Utils;
use App\Subject_Class;
use App\Subject_Class_Teacher;
use App\Period_Attendance;
use App\Open_Period;
use App\Attendance;
use App\Klass;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TeacherController extends Controller
{
    public function login()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            Auth::logout();
        }
        return view('teacher.login');
    }

    public function attendanceDetails()
    {
        $years = Klass::getAssociatedClass(Teacher::getCurrentTeacher()->teacher_id);
        return view('teacher.student_attendance_details')->with(['years' => $years]);
    }

    public function absentList()
    {
        $years = Klass::getAssociatedClass(Teacher::getCurrentTeacher()->teacher_id);
        return view('teacher.students_absent_list')->with(['years' => $years]);
    }

    public function attendancePercentage() {
        $years = Klass::getAssociatedClass(Teacher::getCurrentTeacher()->teacher_id);
        $class_id = Input::get('class_id');
        $selected_month = Input::get('month');
        $studentsAttendance = [];
        if (!is_null($class_id) && !is_null($selected_month)) {
            $year = explode(', ', $selected_month)[1];
            $month = explode(', ', $selected_month)[0];
            $date = Utils::getLastDateOfMonth($month, $year);

            $studentsAttendance = Period_Attendance::getMonthlyAttendanceForClass($class_id, $date);
        }
        $months = Open_Period::getMonths();
        return view('teacher.attendance-percentage')->with(['studentsAttendance' => $studentsAttendance, 'class_id' => $class_id, 'years' => $years, 'months' => $months->toArray(), 'selected_month' => $selected_month]);
    }

    public function getStudentAttendanceDetails(Request $request)
    {
        $roll_no = $request->roll_no;
        $from = $request->from;
        $to = $request->to;

        $student = Student::where('roll_no', $roll_no)->first();
        if (!$student) {
            return response(null, '204');
        }
        $class = Student::where('roll_no', $roll_no)->first()->klass;
        $class_id = $class->class_id;

        $result = Period_Attendance::getAttendanceDetails($roll_no, $from, $to);

        $response = [];
        $last_date = '';
        $attendances = [];
        foreach ($result as $value) {
            if ($last_date != $value->date) {
                $attendances = [];
                $last_date = $value->date;
                array_push($response, []);
            }

            $data = new \stdClass();
            $data->subject_code = $value->subject_code;
            $data->present = $value->present;
            $data->period_num = $value->period_num;
            $attendances[$value->period_num] = $data;

            $response[count($response) - 1]['date'] = $value->date;
            $response[count($response) - 1]['attendances'] = $attendances;
        }

        $timetables = [];
        foreach ($response as $key => $value) {
            $date = $value['date'];
            $attendances = $value['attendances'];
            $day = Utils::getDayFromDate($date);

            if (!array_key_exists($day, $timetables)) {
                $timetable = Period::getTimetable($day, $class_id);
                $timetables[$day] = $timetable;
            }

            foreach ($timetables[$day] as $period) {
                if (!array_key_exists($period->period_num, $value['attendances'])) {
                    $free_period = Subject_Class::getFreeSubjectClass($class_id);
                    $lunch_period = Subject_Class::getLunchBreakSubjectClassId($class_id);

                    $data = new \stdClass();
                    if ($period->subject_class_id == $free_period->subject_class_id) {
                        $data->subject_code = '';
                    } else if ($period->subject_class_id == $lunch_period->subject_class_id) {
                        continue;
                    } else {
                        $data->subject_code = $period->subject_class->subject->subject_code;
                    }
                    $data->period_num = $period->period_num;
                    $data->present = -1;

                    $response[$key]['attendances'][$period->period_num] = $data;
                }
            }

        }

        return response(json_encode($response), '200');
    }

    public function getStudentsAbsentForThreeDaysOrAbove(Request $request)
    {
        $class_id = $request->class_id;
        $from = $request->from;
        $to = $request->to;

        $response = Student::getStudentsAbsentForThreeDaysOrAbove($class_id, $from, $to);
        return response(json_encode($response), 200);
    }

    public function getStudentsAbsentList(Request $request)
    {
        $class_id = $request->class_id;
        $from = $request->from;
        $to = $request->to;

        $absent_list = Period_Attendance::getStudentsAbsentList($class_id, $from, $to);
        $list = [];
        foreach ($absent_list as $value) {
            $list[$value->date] = $value->absent_students;
        }

        $response = [];
        $date = new \DateTime($from);
        $end_date = new \DateTime($to);
        while ($date <= $end_date) {
            $data = [];
            $data['date'] = $date->format('Y-m-d');
            if (array_key_exists($date->format('Y-m-d'), $list)) {
                $data['absent_students'] = $list[$date->format('Y-m-d')];
                array_push($response, $data);
            } else {
                $day = Utils::getDayFromDate($date->format('Y-m-d'));
                if ($day != 0 && $day != 6) {
                    $is_open = Open_Period::isOpen($class_id, $date->format('Y-m-d'))->is_open;
                    if ($is_open == 1) {
                        $data['absent_students'] = null;
                        array_push($response, $data);
                    }
                }
            }
            $date->modify('+1 day');
        }
        return response(json_encode($response), '200');
    }

    public function getStudent()
    {
        $roll_no = Input::get('roll_no');
        $student = Student::where('roll_no', $roll_no)->first();

        return (is_null($student)) ? 'null' : $student;
    }

    public function timetable()
    {
        $period = new Period();
        $date = Input::get('date');
        $msgCode = Input::get('msg_code');

        if (!is_null($date) && !Utils::validateDate($date)) {
            return "Invalid date format!";
        }

        $teacher_id = Teacher::getCurrentTeacher()->teacher_id;
        $currentDay = date('N');
        $tempTimetable = $period->getTeacherTimetable($teacher_id,
            (!is_null($date)) ? Utils::getDayFromDate($date) : (($currentDay != 6 && $currentDay != 7) ? $currentDay : 1));

        $timetable = [];
        $tempArray = [];

        if (!is_null($tempTimetable) && sizeof($tempTimetable) > 0) {
            $lastPeriod = $tempTimetable[0];
            foreach ($tempTimetable as $period) {
                if ($period->period_num != $lastPeriod->period_num) {
                    array_push($timetable, Utils::getAssociatedPeriod($tempArray, $date));
                    $tempArray = [];
                }
                array_push($tempArray, $period);
                $lastPeriod = $period;
            }
            array_push($timetable, Utils::getAssociatedPeriod($tempArray, $date));
        }

        usort($timetable, function ($item1, $item2) {
            return $item1->period_num > $item2->period_num;
        });

        $with = ['timetables' => $timetable, 'dates' => Utils::getDatesInThisWeek()];
        $with['selectedDate'] = (!is_null($date)) ? $date : Utils::getDefaultDate();
        $with['msgCode'] = (!is_null($msgCode)) ? $msgCode : 0;

        return view('teacher.timetable')->with($with);
    }

    public function studentAttendance()
    {
        $teacher_id = Teacher::getCurrentTeacher()->teacher_id;
        $classes = Klass::getClassesOfTeacher($teacher_id);

        $klass = Input::get('class');
        $subject = Input::get('subject');

        $attendances = [];

        if (!is_null($klass) && !is_null($subject)) {
            if (Subject_Class_Teacher::checkIfSubjectClassIsOfTeacher($subject, $klass, $teacher_id))
                $attendances = Attendance::getAttendanceForSubject($klass, $subject);
            else
                return "You can only check attendance of subjects you teach.";
        }

        return view('teacher.student_attendance')->with(['klasses' => $classes, 'attendances' => $attendances, 'class_id' => ($klass) ? $klass : 0,
            'subject_id' => ($subject) ? $subject : 0]);
    }

    public function addAttendance($period_ids)
    {
        $date = Input::get('date');
        $periods = explode(',', $period_ids);
        $error = $this->check($date, $periods);
        $periodObjects = Period::find($periods);
        $numberOfPeriods = Period::getUniquePeriodNumber($periods);
        $student_ids = Student::getStudentsWithMedicalLeave($date);
        if (is_null($error)) {
            $students = Student::getStudentsFromPeriod($periods);
            return view('teacher.add_attendance')->with(['students' => $students, 'periods' => $periods, 'date' => $date,
                'periodObjects' => $periodObjects, 'numberOfPeriods' => $numberOfPeriods, 'attendedStudents' => $this->getAttendedStudentsFromPeriods($periods, $date),
                'studentsWithMedicalLeaves' => array_column($student_ids->toArray(), 'student_id')]);
        } else {
            return $error;
        }
    }

    private function check($date, $periods)
    {
        if (Utils::validateDate($date)) {
            if (Period::checkIfPeriodsAreTaughtByCurrentTeacher($periods)) {
                if (Utils::checkDateIsEligible($date)) {
                    foreach ($periods as $period) {
                        if (Utils::periodIsInDate($periods, $date)) {
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

    private function getAttendedStudentsFromPeriods($period_ids, $date)
    {
        $attendedStudents = null;
        $students = null;
        foreach ($period_ids as $period_id) {
            $openPeriod = Open_Period::fetch($period_id, $date);
            if (!is_null($openPeriod)) $students = $openPeriod->attendedStudents;
            if (!is_null($students)) {
                if (is_null($attendedStudents)) $attendedStudents = [];
                $attendedStudents[$period_id . '_student'] = $students;
            } else {
                if (!is_null($attendedStudents)) $attendedStudents[$period_id . '_student'] = [];
            }
        }
        return $attendedStudents;
    }

    public function saveOrEditAttendance()
    {
        $date = Input::get('date');
        $presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        $error = $this->check($date, $periods);

        if (is_null($error)) {
            foreach ($period_ids as $period_id) {
                $key = $period_id . '_student';
                $students = Input::post($key);
                $presentStudents[$key] = (is_null($students)) ? [] : $students;
            }

            $isUpdate = !is_null($this->getAttendedStudentsFromPeriods($period_ids, $date));

            if (!$isUpdate) {
                Period_Attendance::saveAttendance($period_ids, $date, $presentStudents);
            } else {
                Period_Attendance::updateAttendance($period_ids, $date, $presentStudents);
            }

            return redirect()->action('TeacherController@timetable', ['msg_code' => ($isUpdate) ? '2' : '1', 'date' => $date]);
        } else {
            return $error;
        }
    }
}
