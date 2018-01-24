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
            ->join('students', 'students.roll_no', '=', 'period_attendance.roll_no')
            ->join('open_periods', 'open_periods.open_period_id', '=', 'period_attendance.open_period_id')
            ->join('periods', 'periods.period_id', '=', 'open_periods.period_id')
            ->join('subject_class', function($join) {
                $join->on('periods.subject_class_id', '=', 'subject_class.subject_class_id')
                     ->on('students.class_id', '=', 'subject_class.class_id');
            })
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->whereBetween('open_periods.date', [$from, $to])
            ->where('students.roll_no', $roll_no)
            ->select('open_periods.open_period_id', 'open_periods.date', 'periods.period_num', 'subjects.subject_code', 'period_attendance.present')
            ->orderby('open_periods.date')
            ->orderby('periods.period_num')
            ->get();
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

    public static function saveAttendance($period_ids, $date, $presentStudents) {
        $date = strtotime($date);
        $date = date('Y-m-d', $date);

        foreach ($period_ids as $periodId) {
            $klass = Klass::getClassFromPeriod($periodId);
            
            $openPeriod = Open_Period::firstOrNew(array('date' => $date, 'period_id' => $periodId));
            $openPeriod->save();

            $openPeriodId = $openPeriod->open_period_id;
            
            $students = Student::where('class_id', $klass->class_id)->get();

            foreach ($students as $value) {
                $student_id = $value['student_id'];

                $periodAttendance = new Period_Attendance();
                $periodAttendance->student_id = $student_id;
                $periodAttendance->open_period_id = $openPeriodId;
                $periodAttendance->present = in_array($student_id, $presentStudents[$periodId . '_student']);
                $periodAttendance->save();

                $studentAttendance = Period_Attendance::getStudentAttendance($student_id);
                Attendance::updateStudentAttendance($student_id, $studentAttendance);
            }

            //InternalLog::addLog('ADD', 'Add blah blah blah...', $openPeriod->attendedStudents, null, Utils::getCurrentDateTime(), 1);
        }
    }

    public static function updateAttendance($period_ids, $date, $presentStudents) {
        foreach ($period_ids as $periodId) {
            $openPeriod = Open_Period::where('date', $date)
                ->where('period_id', $periodId)
                ->first();

            print_r($openPeriod);


            // $log = new InternalLog();
            // $log->old_value = $openPeriod->attendedStudents->toArray();
            // $log->by_user = 1;  // add user_id here
            // $log->created_at = Utils::getCurrentDateTime();
            // $log->action = 'EDIT';
            // $log->message = 'EDIT blah blah blah...';
            // $log->save();
            
            foreach ($openPeriod->attendedStudents as $periodAttendance) {
                $student_id = $periodAttendance['student_id'];

                $periodAttendance->present = in_array($student_id, $presentStudents[$periodId . '_student']);
                $periodAttendance->save();

                $studentAttendance = Period_Attendance::getStudentAttendance($student_id);
                Attendance::updateStudentAttendance($student_id, $studentAttendance);
            }

            // $log->new_value = $openPeriod->attendedStudents;
            // $log->save();
        }
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
                AND period_attendance.roll_no = students.roll_no
                AND classes.class_id = :class_id
                AND open_periods.date BETWEEN :from_date AND :to_date
                AND students.class_id = classes.class_id
                AND subject_class.class_id = classes.class_id
                AND periods.subject_class_id = subject_class.subject_class_id
                AND open_periods.period_id = periods.period_id
                GROUP BY period_attendance.roll_no, open_periods.date) AS t1
            WHERE t1.total = 0
            GROUP BY t1.date
            ORDER BY t1.date;"
        ), array('class_id' => $class_id, 'from_date' => $from, 'to_date' => $to));
    }
}
