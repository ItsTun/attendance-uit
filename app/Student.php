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

    public function getStudentFromPeriod($period_id) {
        return DB::table('periods')
            ->join('subjects', 'subjects.subject_id', '=', 'periods.subject_id')
            ->join('students', 'students.class_id', '=', 'subjects.class_id')
            ->where('periods.period_id', $period_id)
            ->select('students.*')
            ->get();
    }

    // public function scopeAttendedPeriods($query, $month) {
    // 	return $query->where('month');
    // }
}
