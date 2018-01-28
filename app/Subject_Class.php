<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Subject;

class Subject_Class extends Model
{
    protected $table = "subject_class";
    protected $primaryKey = "subject_class_id";

    public function subject() {
    	return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function klass() {
    	return $this->belongsTo(Klass::class, 'class_id');
    }

    public static function getSubjectClass($subject_id, $class_id) {
    	return Subject_Class::where('subject_id', $subject_id)->where('class_id', $class_id)->first();
    }

    public static function getLunchBreakSubjectClassId($class_id) {
        return Subject_Class::whereNull('subject_id')->where('class_id', $class_id)->first();
    }

    public static function getSubjectsFromClass($class_id) {
        $free_subject_class_id = Subject_Class::getFreeSubjectClass($class_id);
        return Subject_Class::where('class_id', $class_id)->where('subject_class_id', '<>', $free_subject_class_id->subject_class_id)->get();
    }

    public static function getSubjectClassIdsFromClass($class_id) {
        return Subject_Class::where('class_id', $class_id)->select('subject_class_id')->get();
    }

    public static function getFreeSubjectClass($classId) {
        $subject = Subject::where('subject_code', '000')->first();
        $free_subject_class = Subject_Class::where('subject_id', $subject->subject_id)->where('class_id', $classId)->first();
        return $free_subject_class;
    }
}
