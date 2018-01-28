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

	public static function getAttendanceDetails($roll_no, $from, $to) {
	   return DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('open_periods', 'open_periods.open_period_id', '=', 'period_attendance.open_period_id')
            ->join('periods', 'periods.period_id', '=', 'open_periods.period_id')
            ->join('subject_class', function($join) {
                $join->on('periods.subject_class_id', '=', 'subject_class.subject_class_id')
                     ->on('students.class_id', '=', 'subject_class.class_id');
            })
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->whereBetween('open_periods.date', [$from, $to])
            ->where('students.roll_no', '=',$roll_no)
            ->select('open_periods.open_period_id', 'open_periods.date', 'periods.period_num', 'subjects.subject_code', 'period_attendance.present')
            ->orderby('open_periods.date')
            ->orderby('periods.period_num')
            ->get();
	}

	public static function getDailyDetail(Student $student, $date) {
		return DB::table('period_attendance')
            ->join('students', 'students.roll_no', '=', 'period_attendance.roll_no')
            ->join('open_periods', 'period_attendance.open_period_id', '=', 'open_periods.open_period_id')
            ->join('periods', 'open_periods.period_id', '=', 'periods.period_id')
            ->where('open_periods.date', $date)
            ->where('students.roll_no', $student->roll_no)
            ->select('periods.period_id', 'period_attendance.present')
            ->orderby('periods.period_num')
            ->get();
	}

    public static function getStudentAttendance($student_id) {
        return DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('classes', 'classes.class_id', '=', 'students.class_id')
            ->join('subject_class', 'subject_class.class_id', '=', 'classes.class_id')
            ->join('periods', 'periods.subject_class_id', '=', 'subject_class.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->join('open_periods', function($join) {
                $join->on('open_periods.period_id', '=', 'periods.period_id')->on('period_attendance.open_period_id', '=', 'open_periods.open_period_id');
            })
            ->where('period_attendance.student_id', $student_id)
            ->select('subject_class.subject_class_id', 
                    'subjects.subject_code', 
                    'subjects.name',
                    'classes.name as class_name',
                    DB::raw('COUNT(open_periods.open_period_id) as total_periods'), 
                    DB::raw('SUM(period_attendance.present) as attended_periods'), 
                    DB::raw('SUM(period_attendance.present)/COUNT(open_periods.open_period_id) * 100 as percent'))
            ->groupby('subject_class.subject_class_id')
            ->get();
    }

    public static function getMonthlyAttendance($roll_no, $subject_class_id) {
        return DB::table('period_attendance')
                    ->join('students', 'students.roll_no', '=', 'period_attendance.roll_no')
                    ->join('open_periods', 'open_periods.open_period_id', '=', 'period_attendance.open_period_id')
                    ->join('periods', 'periods.period_id', '=', 'open_periods.period_id')
                    ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
                    ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
                    ->where('students.roll_no', $roll_no)
                    ->where('subject_class.subject_class_id', $subject_class_id)
                    ->select(DB::raw('COUNT(period_attendance.open_period_id) AS periods'), 
                            DB::raw('SUM(period_attendance.present) AS present'), 
                            DB::raw('(COUNT(period_attendance.open_period_id) - SUM(period_attendance.present)) AS absent'),
                            DB::raw('MONTH(open_periods.date) AS month'))
                    ->groupby(DB::raw('YEAR(open_periods.date), MONTH(open_periods.date)'))
                    ->orderby(DB::raw('YEAR(open_periods.date), MONTH(open_periods.date)'))
                    ->get();
    }

    public static function getStudentsAbsentList($class_id, $from, $to) {
        return DB::select( DB::raw(
            "SELECT t1.date, GROUP_CONCAT(t1.roll_no) AS absent_students FROM (
                SELECT open_periods.date, SUM(period_attendance.present) AS total, students.roll_no
                FROM open_periods, period_attendance, students, classes, subject_class, periods
                WHERE period_attendance.open_period_id = open_periods.open_period_id
                AND period_attendance.student_id = students.student_id
                AND classes.class_id = :class_id
                AND open_periods.date BETWEEN :from_date AND :to_date
                AND students.class_id = classes.class_id
                AND subject_class.class_id = classes.class_id
                AND periods.subject_class_id = subject_class.subject_class_id
                AND open_periods.period_id = periods.period_id
                GROUP BY period_attendance.student_id, open_periods.date) AS t1
            WHERE t1.total = 0
            GROUP BY t1.date
            ORDER BY t1.date;"
        ), array('class_id' => $class_id, 'from_date' => $from, 'to_date' => $to));
    }
}
