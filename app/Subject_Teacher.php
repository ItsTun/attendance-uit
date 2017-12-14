<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject_Teacher extends Model
{
    protected $table = "subject_teacher";

    public function subject() {
    	return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher() {
    	return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
