<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\Klass;
use App\Open_Period;

class Student extends Model
{
    public $incrementing = false;

    protected $table = "students";
    protected $primaryKey = "student_id";
    protected $fillable = ['roll_no', 'name', 'email', 'class_id'];

    public static function getStudents($query, $roll_no, $class_id)
    {
        $q = Student::where('name', 'like', '%' . $query . '%');
        if (!is_null($roll_no)) $q->where('roll_no', 'like', '%' . $roll_no . '%');
        if (!is_null($class_id) && $class_id != -1) $q->where('class_id', $class_id);
        $students = $q->paginate(PaginationUtils::getDefaultPageSize());
        return $students;
    }

    public static function getStudentsFromPeriod($period_ids)
    {
        return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('students', 'students.class_id', '=', 'subject_class.class_id')
            ->orWhereIn('periods.period_id', $period_ids)
            ->select('students.*')
            ->distinct()
            ->get();
    }

    public static function getStudentsFromYearOrClass($year_id, $class_id)
    {
        if (!is_null($class_id) && $class_id != -1) {
            return Student::where('class_id', $class_id)->get();
        } else {
            $classes = Klass::where('year_id', $year_id)->get();
            return Student::whereIn('class_id', $classes)->orderBy('roll_no')->get();
        }
    }

    public static function getStudentsFromClass($class_id)
    {
        return Student::where('class_id', $class_id)->groupBy('updated_at')->groupBy('roll_no')->orderBy(DB::raw('length(roll_no)'), 'ASC')->orderBy('roll_no')->get();
    }

    public static function getStudentByEmail($email)
    {
        return DB::table('students')
            ->join('classes', 'classes.class_id', '=', 'students.class_id')
            ->where('students.email', '=', $email)
            ->select('students.roll_no', 'students.name', 'students.email', 'classes.short_form as class_short_form', 'classes.name as class_name')
            ->get();
    }

    public static function getStudentsWithRollNos($roll_nos)
    {
        return DB::table('students')
            ->whereIn('roll_no', $roll_nos)
            ->get();
    }

    public static function getStudentsWithEmails($emails)
    {
        return DB::table('students')
            ->whereIn('email', $emails)
            ->get();
    }

    public static function getStudentsWithMedicalLeave($date)
    {
        return MedicalLeave::where('leave_from', '<=', $date)
            ->where('leave_to', '>=', $date)
            ->select('student_id')->get();
    }

    public function klass()
    {
        return $this->belongsTo(Klass::class, 'class_id', 'class_id');
    }

    public static function getAllStudentAbsentsForThreeDaysOrAbove($class_id) {
        $from = Open_Period::getFirstDate();
        $to = Open_Period::getLastDate();
        return Student::getStudentsAbsentForThreeDaysOrAbove($class_id, $from, $to);
    }

    public static function getStudentsAbsentForThreeDaysOrAbove($class_id, $from, $to) {
        $response = [];
        $absent_students = Period_Attendance::getStudentsAbsentDays($class_id, $from, $to);
        foreach ($absent_students as $student) {
            $absent_dates_ary = explode(',', $student->absent_dates);
            sort($absent_dates_ary);

            $start = null; $end = null; $count = 0;
            $total_absences = []; $absent_dates = [];
            foreach ($absent_dates_ary as $date_str) {
                $current_date = new DateTime($date_str);
                $aday_before = new DateTime($current_date->format('Y-m-d'));
                $aday_before->modify('-1 day');

                while (Utils::getDayFromDate($aday_before->format('Y-m-d')) == 0 
                    || Utils::getDayFromDate($aday_before->format('Y-m-d')) == 6) {
                    $aday_before->modify('-1 day');
                }

                if ($end == $aday_before) {
                    $end = new DateTime($current_date->format('Y-m-d'));
                    $count++;
                } else {
                    if ($count >= 3) {
                        array_push($total_absences, $count);
                        $date_range['from'] = $start->format('Y-m-d');
                        $date_range['to'] = $end->format('Y-m-d');
                        array_push($absent_dates, $date_range);
                    }
                    $start = new DateTime($current_date->format('Y-m-d'));
                    $end = new DateTime($current_date->format('Y-m-d'));
                    $count = 1;
                }
            }
            if ($count >= 3) {
                array_push($total_absences, $count);
                $date_range['from'] = $start->format('Y-m-d');
                $date_range['to'] = $end->format('Y-m-d');
                array_push($absent_dates, $date_range);
            }
            if (count($total_absences) != 0 && count($absent_dates) != 0) {
                $data['student_id'] = $student->student_id;
                $data['roll_no'] = $student->roll_no;
                $data['name'] = $student->name;
                $data['email'] = $student->email;
                $data['total_absences'] = $total_absences;
                $data['absent_dates'] = $absent_dates;
                array_push($response, $data);
            }
        }
        return $response;
    }
}
