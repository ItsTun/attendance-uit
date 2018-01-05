<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Klass extends Model
{
	protected $table = "classes";
	protected $primaryKey = "class_id";

	public function students() {
		return $this->hasMany(Student::class, 'class_id');
	}

	public function subject_class() {
		return $this->hasMany(Subject_Class::class, 'class_id');
	}

	public function year() {
		return $this->belongsTo(Year::class, 'year_id');
	}

	public static function getClasses() {
		return Klass::paginate(PaginationUtils::getDefaultPageSize());
	}

	public static function getClassesOfTeacher($teacher_id) {
		return DB::table('teachers')
			->join('subject_class_teacher', 'subject_class_teacher.teacher_id', 'teachers.teacher_id')
			->join('subject_class', 'subject_class.subject_class_id', 'subject_class_teacher.subject_class_id')
			->join('classes', 'classes.class_id', 'subject_class.class_id')
			->join('subjects', 'subjects.subject_id', 'subject_class.subject_id')
			->join('years', 'classes.year_id', 'years.year_id')
			->where('teachers.teacher_id', '=', $teacher_id)
			->select('classes.*', 'years.name as year_name', 'subjects.subject_id','subjects.subject_code','subjects.name as subject_name')
			->get();
	}

	public static function getClassFromPeriod($period_id) {
		return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->where('periods.period_id', $period_id)
            ->select('subject_class.class_id')
            ->first();
	}
}
