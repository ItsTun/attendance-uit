<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject_Class_Teacher extends Model
{
    protected $table = "subject_class_teacher";

    public function subject_class() {
    	return $this->belongsTo(Subject_Class::class, 'subject_class_id');
    }

    public function teacher() {
    	return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public static function checkIfSubjectClassIsOfTeacher($subject_id, $class_id, $teacher_id) {
    	$subject_class = Subject_Class::getSubjectClass($subject_id, $class_id);
		$subject_class_teacher = Subject_Class_Teacher::where('subject_class_id', $subject_class->subject_class_id)
									->where('teacher_id', $teacher_id)->first();
		return !is_null($subject_class_teacher);
    }
}
