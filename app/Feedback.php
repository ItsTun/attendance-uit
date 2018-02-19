<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $primaryKey = 'feedback_id';
    protected $fillable = ['student_id', 'device_type', 'content'];

    public function student() {
    	return $this->belongsTo(Student::class, 'student_id');
    }
}
