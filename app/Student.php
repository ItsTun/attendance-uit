<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	
    protected $table = "students";
    protected $primaryKey = "roll_no";

    public function klass() {
    	return $this->belongsTo(Klass::class, 'class_id', 'class_id');
    }

    public static function getStudentsFromPeriod($period_id) {
        return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('students', 'students.class_id', '=', 'subject_class.class_id')
            ->where('periods.period_id', $period_id)
            ->select('students.*')
            ->get();
    }

    public function scopeEmail($query, $email) {
        return $query->where('email', '=',$email);
    }
}
