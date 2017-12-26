<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class Period extends Model
{
    protected $table = "periods";
    protected $primaryKey = "period_id";

    public function subject_class() {
    	return $this->belongsTo(Subject_Class::class, 'subject_class_id');
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
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('subject_teacher', 'subject_teacher.subject_class_id', '=', 'subject_class.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->where('subject_teacher.teacher_id', $teacher_id)
            ->where('periods.day', $day)
            ->select('subjects.*', 'periods.*')
            ->orderby('period_num')
            ->get();
    }

    public static function checkPeriodsAreTaughtByCurrentTeacher($period_ids) {
        $logged_in_teacher = Teacher::where('email', Auth::user()->email)->first();
        
        $subject_class_ids = [];
        foreach($logged_in_teacher->subject_teachers as $subject_teacher) {
            array_push($subject_class_ids, $subject_teacher->subject_class->subject_class_id);
        }

        foreach($period_ids as $period_id) {
            $period = Period::find($period_id);
            $subject_class = $period->subject_class;
            if(!in_array($subject_class->subject_class_id, $subject_class_ids)) return false;
        }

        return true;
    }

    public static function checkPeriodsAreOfSameSubjectAndClass($period_ids) {
        $subject_class_id = 0;
        
        foreach($period_ids as $period_id) {
            $period = Period::find($period_id);
            $subject_class = $period->subject_class;
            if($subject_class_id == 0) {
                $subject_class_id = $subject_class->subject_class_id;
            } else {
                if($subject_class_id != $subject_class->subject_class_id) return false;
            }
        }
        return true;
    }
}
