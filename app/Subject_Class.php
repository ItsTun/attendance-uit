<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return Subject_Class::where('class_id', $class_id)->get();
    }

    public static function getSubjectClassIdsFromClass($class_id) {
        return Subject_Class::where('class_id', $class_id)->select('subject_class_id')->get();
    }
}
