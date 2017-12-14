<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Period extends Model
{
    protected $table = "periods";
    protected $primaryKey = "period_id";

    public function subjects() {
    	return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function getTimetable($klassId, $day) {

    	$results = DB::select( DB::raw(
    		'SELECT periods.period_id, subjects.subject_code, subjects.name subject_name, periods.duration, periods.period_num, periods.room, GROUP_CONCAT(teachers.name) teacher_name
			FROM periods, subjects, subject_teacher, teachers
			WHERE subjects.class_id = :class_id 
            AND periods.subject_id = subjects.subject_id 
            AND periods.day = :day
            AND teachers.teacher_id = subject_teacher.teacher_id
            AND subjects.subject_id = subject_teacher.subject_id
            GROUP BY periods.period_id
			ORDER BY periods.period_num;'
    	), array('day' => $day, 'class_id' => $klassId) );

    	return $results;

    }

    public function getTeacherTimetable($teacher_id, $day) {
        return DB::table('periods')
            ->join('subject_teacher', 'subject_teacher.subject_id', '=', 'periods.subject_id')
            ->join('subjects', 'subjects.subject_id', '=', 'periods.subject_id')
            ->where('subject_teacher.teacher_id', $teacher_id)
            ->where('periods.day', $day)
            ->select('subjects.*', 'periods.*')
            ->orderby('period_num')
            ->get();
    }
}
