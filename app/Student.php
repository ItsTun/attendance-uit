<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	
    protected $table = "students";
    protected $primaryKey = "roll_no";

    public function klass() {
    	return $this->belongsTo(Klass::class, 'class_id', 'class_id');
    }

    public static function getStudentsFromPeriod($period_ids) {
        return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('students', 'students.class_id', '=', 'subject_class.class_id')
            ->orWhereIn('periods.period_id', $period_ids)
            ->select('students.*')
            ->distinct()
            ->get();
    }

    public static function getStudentsFromClass($class_id) {
        return Student::where('class_id', $class_id)->get();
    }

    public static function getStudentByEmail($email) {
        return DB::table('students')
            ->join('classes', 'classes.class_id', '=', 'students.class_id')
            ->where('students.email', '=', $email)
            ->select('students.roll_no', 'students.name', 'students.email', 'classes.short_form as class_short_form', 'classes.name as class_name')
            ->get();
    }
}
