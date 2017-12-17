<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = "teachers";
    protected $primaryKey = "teacher_id";

    public function subject_teachers() {
    	return $this->hasMany(Subject_Teacher::class, 'teacher_id');
    }
}
