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
            
            $students = Student::select('roll_no')->where('class_id', $klass->class_id)->get();

            foreach ($students as $value) {
                $rollNo = $value['roll_no'];

                $periodAttendance = new Period_Attendance();
                $periodAttendance->roll_no = $rollNo;
                $periodAttendance->open_period_id = $openPeriodId;
                $periodAttendance->present = in_array($rollNo, $presentStudents[$periodId . '_student']);
                $periodAttendance->save();

                $studentAttendance = Period_Attendance::getStudentAttendance($rollNo);
                Attendance::updateStudentAttendance($rollNo, $studentAttendance);
            }

            InternalLog::addLog('ADD', 'Add blah blah blah...', $openPeriod->attendedStudents, null, Utils::getCurrentDateTime(), 1); // change user_id
        }
    }

    public static function updateAttendance($period_ids, $date, $presentStudents) {
        foreach ($period_ids as $periodId) {
            $openPeriod = Open_Period::where('date', $date)
                ->where('period_id', $periodId)
                ->first();


            // $log = new InternalLog();
            // $log->old_value = $openPeriod->attendedStudents->toArray();
            // $log->by_user = 1;  // add user_id here
            // $log->created_at = Utils::getCurrentDateTime();
            // $log->action = 'EDIT';
            // $log->message = 'EDIT blah blah blah...';
            // $log->save();
            
            foreach ($openPeriod->attendedStudents as $periodAttendance) {
                $rollNo = $periodAttendance['roll_no'];

                $periodAttendance->present = in_array($rollNo, $presentStudents[$periodId . '_student']);
                $periodAttendance->save();

                $studentAttendance = Period_Attendance::getStudentAttendance($rollNo);
                Attendance::updateStudentAttendance($rollNo, $studentAttendance);
            }

            // $log->new_value = $openPeriod->attendedStudents;
            // $log->save();
        }
    }

    public static function getStudentAttendance($rollNo) {
        return DB::table('period_attendance')
            ->join('students', 'students.roll_no', '=', 'period_attendance.roll_no')
            ->join('classes', 'classes.class_id', '=', 'students.class_id')
            ->join('subject_class', 'subject_class.class_id', '=', 'classes.class_id')
            ->join('periods', 'periods.subject_class_id', '=', 'subject_class.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->join('open_periods', function($join) {
                $join->on('open_periods.period_id', '=', 'periods.period_id')->on('period_attendance.open_period_id', '=', 'open_periods.open_period_id');
            })
            ->where('period_attendance.roll_no', $rollNo)
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
}
