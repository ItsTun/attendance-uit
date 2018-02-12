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

    public static function getAttendanceDetails($roll_no, $from, $to)
    {
        return DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('open_periods', 'open_periods.open_period_id', '=', 'period_attendance.open_period_id')
            ->join('periods', 'periods.period_id', '=', 'open_periods.period_id')
            ->join('subject_class', function ($join) {
                $join->on('periods.subject_class_id', '=', 'subject_class.subject_class_id')
                    ->on('students.class_id', '=', 'subject_class.class_id');
            })
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->whereBetween('open_periods.date', [$from, $to])
            ->where('students.roll_no', '=', $roll_no)
            ->select('open_periods.open_period_id', 'open_periods.date', 'periods.period_num', 'subjects.subject_code', 'period_attendance.present')
            ->orderby('open_periods.date')
            ->orderby('periods.period_num')
            ->get();
    }

    public static function getDailyDetail(Student $student, $date)
    {
        return DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('open_periods', 'period_attendance.open_period_id', '=', 'open_periods.open_period_id')
            ->join('periods', 'open_periods.period_id', '=', 'periods.period_id')
            ->where('open_periods.date', $date)
            ->where('students.roll_no', $student->roll_no)
            ->select('periods.period_id', 'periods.period_num', 'period_attendance.present')
            ->orderby('periods.period_num')
            ->get();
    }

    public static function getMonthlyAttendance($student_id, $subject_class_id)
    {
        return DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('open_periods', 'open_periods.open_period_id', '=', 'period_attendance.open_period_id')
            ->join('periods', 'periods.period_id', '=', 'open_periods.period_id')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->where('students.student_id', $student_id)
            ->where('subject_class.subject_class_id', $subject_class_id)
            ->select(DB::raw('COUNT(period_attendance.open_period_id) AS periods'),
                DB::raw('SUM(period_attendance.present) AS present'),
                DB::raw('(COUNT(period_attendance.open_period_id) - SUM(period_attendance.present)) AS absent'),
                DB::raw('MONTH(open_periods.date) AS month'))
            ->groupby(DB::raw('YEAR(open_periods.date), MONTH(open_periods.date)'))
            ->orderby(DB::raw('YEAR(open_periods.date), MONTH(open_periods.date)'))
            ->get();
    }

    public static function getStudentsAbsentList($class_id, $from, $to)
    {
        return DB::select(DB::raw(
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
                GROUP BY period_attendance.student_id, open_periods.date
                ORDER BY LENGTH(students.roll_no), students.roll_no) AS t1
            WHERE t1.total = 0
            GROUP BY t1.date
            ORDER BY t1.date;"
        ), array('class_id' => $class_id, 'from_date' => $from, 'to_date' => $to));
    }

    public static function getStudentsAbsentDays($class_id, $from, $to)
    {
        return DB::select(DB::raw(
            "SELECT t.student_id, t.roll_no, t.name, t.email, GROUP_CONCAT(t.date) AS absent_dates FROM 
                (SELECT open_periods.date, SUM(period_attendance.present) AS total, students.student_id, students.roll_no, students.name, students.email
                            FROM open_periods, period_attendance, students, classes, subject_class, periods
                            WHERE period_attendance.open_period_id = open_periods.open_period_id
                            AND period_attendance.student_id = students.student_id
                            AND classes.class_id = :class_id
                            AND open_periods.date BETWEEN :from_date AND :to_date
                            AND students.class_id = classes.class_id
                            AND subject_class.class_id = classes.class_id
                            AND periods.subject_class_id = subject_class.subject_class_id
                            AND open_periods.period_id = periods.period_id
                            GROUP BY period_attendance.student_id, open_periods.date
                            ORDER BY LENGTH(students.roll_no), students.roll_no, open_periods.date) AS t
                WHERE t.total = 0
                GROUP BY t.roll_no
                ORDER BY LENGTH(t.roll_no), t.roll_no;"
        ), array('class_id' => $class_id, 'from_date' => $from, 'to_date' => $to));
    }

    public static function saveAttendance($period_ids, $date, $presentStudents)
    {
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
                $studentAttendance = $studentAttendance->toArray();
                if (sizeof($studentAttendance) > 0) foreach ($studentAttendance as $attendance) {
                    $attendance->attended_periods = intval($attendance->attended_periods);
                    $attendance->percent = floatval($attendance->percent);
                }
                Attendance::updateStudentAttendance($student_id, $studentAttendance);
            }
        }
    }

    public static function getStudentAttendance($student_id)
    {
        return DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('classes', 'classes.class_id', '=', 'students.class_id')
            ->join('subject_class', 'subject_class.class_id', '=', 'classes.class_id')
            ->join('periods', 'periods.subject_class_id', '=', 'subject_class.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->join('open_periods', function ($join) {
                $join->on('open_periods.period_id', '=', 'periods.period_id')
                    ->on('period_attendance.open_period_id', '=', 'open_periods.open_period_id');
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

    public static function updateAttendance($period_ids, $date, $presentStudents)
    {
        foreach ($period_ids as $periodId) {
            $openPeriod = Open_Period::where('date', $date)
                ->where('period_id', $periodId)
                ->first();

            foreach ($openPeriod->attendedStudents as $periodAttendance) {
                $student_id = $periodAttendance['student_id'];

                $periodAttendance->present = in_array($student_id, $presentStudents[$periodId . '_student']);
                $periodAttendance->save();

                $studentAttendance = Period_Attendance::getStudentAttendance($student_id);
                $studentAttendance = $studentAttendance->toArray();
                if (sizeof($studentAttendance) > 0) foreach ($studentAttendance as $attendance) {
                    $attendance->attended_periods = intval($attendance->attended_periods);
                    $attendance->percent = floatval($attendance->percent);
                }
                Attendance::updateStudentAttendance($student_id, $studentAttendance);
            }
        }
    }

    public static function setAllPresent($student_id, $open_periods)
    {
        Period_Attendance::where('student_id', $student_id)
            ->whereIn('open_period_id', $open_periods)
            ->update(['present' => 1]);
    }

    public static function getMonthlyAttendanceForClass($class_id, $date)
    {
        $attendances = DB::table('period_attendance')
            ->join('students', 'students.student_id', '=', 'period_attendance.student_id')
            ->join('open_periods', 'open_periods.open_period_id', '=', 'period_attendance.open_period_id')
            ->join('periods', 'periods.period_id', '=', 'open_periods.period_id')
            ->join('classes', 'students.class_id', '=', 'classes.class_id')
            ->join('subject_class', function ($join) {
                $join->on('subject_class.subject_class_id', '=', 'periods.subject_class_id')
                    ->on('subject_class.class_id', '=', 'classes.class_id');
            })
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->where('classes.class_id', $class_id)
            ->where('open_periods.date', '<=', $date)
            ->select('students.student_id', 'students.roll_no', 'students.name', 'subjects.subject_code', DB::raw('SUM(period_attendance.present) / COUNT(period_attendance.open_period_id) * 100 AS percent'))
            ->groupby('students.student_id', 'subjects.subject_code')
            ->orderby(DB::raw('LENGTH(students.roll_no), students.roll_no, subjects.subject_code'))
            ->get();

        $data = [];
        $subject_percents = [];
        $student_id = $attendances[0]->student_id;
        foreach ($attendances as $index => $attendance) {
            if ($student_id != $attendance->student_id || $index == count($attendances) - 1) {
                if ($index == count($attendances) - 1) {
                    $subject_percents[$attendance->subject_code . '##'] = number_format($attendance->percent, 2);
                }
                $student = [];
                $student['Roll No'] = $attendances[$index - 1]->roll_no;
                $student['Name'] = $attendances[$index - 1]->name;
                foreach ($subject_percents as $subject => $percent) {
                    $student[$subject] = $percent;
                }
                $subject_percents = [];
                $student_id = $attendance->student_id;
                array_push($data, $student);
            }
            $subject_percents[$attendance->subject_code . '##'] = number_format($attendance->percent, 2);
        }
        return $data;
    }

}
