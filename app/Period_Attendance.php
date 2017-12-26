<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Student;
use App\Klass;

class Period_Attendance extends Model
{
	public $timestamps = false;

	protected $table = "period_attendance";
	protected $primaryKey = "period_attendance_id";

	public static function getAttendanceDetail($rollNo, $month) {
		$results =  DB::select( DB::raw(
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
        return $results;


	}

	public static function getTotalAbsence($rollNo, $month) {
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
        return $results;
	}

	public static function getAbsentStudentList($klass, $month) {
		$results = DB::select( DB::raw(
            'SELECT students.roll_no, students.name,subjects.subject_code, count(open_periods.open_period_id) - sum(period_attendance.present) as total_absence 
            FROM subjects, periods, open_periods, period_attendance, students
            WHERE periods.subject_id = subjects.subject_id 
            AND open_periods.period_id = periods.period_id 
            AND period_attendance.open_period_id = open_periods.open_period_id 
            AND period_attendance.roll_no = students.roll_no 
            AND subjects.class_id = :klass
            AND MONTH(open_periods.date) = :month 
            GROUP BY students.roll_no, subjects.subject_code
            ORDER BY LENGTH(students.roll_no) ASC, students.roll_no ASC;'
        ), array('klass' => $klass, 'month' => $month) );
        return $results;
	}

	public static function getDailyDetail(Student $student, $date, $dayOfWeek) {
		$results = DB::select( DB::raw(
            'SELECT B.period_id, B.subject_code, B.name,B.period_num, IFNULL(A.present, -1) present
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
            ( SELECT periods.period_id, subjects.subject_code, subjects.name, periods.period_num 
            FROM periods, subjects 
            WHERE subjects.class_id = :klass 
            AND periods.subject_id = subjects.subject_id 
            AND periods.day = :day ) B 
            ON A.period_id = B.period_id
            ORDER BY B.period_num;'
        ), array('roll_no' => $student->roll_no, 'date' => $date, 'klass' => $student->class_id, 'day' => $dayOfWeek) );
        return $results;
	}

    public static function saveAttendance($period_ids, $date, $presentStudents) {
        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        foreach ($period_ids as $periodId) {
            $klass = Klass::getClassFromPeriod($periodId);
            
            $openPeriod = Open_Period::firstOrNew(array('date' => $date, 'period_id' => $periodId));
            $openPeriod->save();

            $openPeriodId = $openPeriod->open_period_id;
            
            $students = Student::select('roll_no')->where('class_id', $klass->class_id)->get();

            foreach ($students as $value) {
                $rollNo = $value['roll_no'];

                $periodAttendance = new Period_Attendance();
                $periodAttendance->roll_no = $rollNo;
                $periodAttendance->open_period_id = $openPeriodId;
                $periodAttendance->present = in_array($rollNo, $presentStudents[$periodId . '_student']);
                $periodAttendance->save();
            }
        }
    }
}
