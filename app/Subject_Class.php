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
}
