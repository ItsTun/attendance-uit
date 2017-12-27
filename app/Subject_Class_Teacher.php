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
}
