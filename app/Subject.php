<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = "subjects";
    protected $primaryKey = "subject_id";

    public function subject_class() {
 		return $this->hasMany(Subject_Class::class, 'subject_id');
    }

    public function subject_teachers() {
    	return $this->hasMany(Subject_Class_Teacher::class, 'subject_id');
    }

    public function subject_class() {
    	return $this->hasMany(Subject_Class::class, 'subject_id');
    }
}
