<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Subject_Class;

class Period extends Model
{
    protected $table = "periods";
    protected $primaryKey = "period_id";

    public function subject_class() {
    	return $this->belongsTo(Subject_Class::class, 'subject_class_id');
    }
    public function subject() {
    	$subjectClass = Subject_Class::find($this->subject_class_id)->first();
        return $subjectClass->subject;
    }

    public static function getTimetable($klassId, $day) {
        return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->join('subject_teacher', 'subject_teacher.subject_id', '=', 'subjects.subject_id')
            ->join('teachers', 'teachers.teacher_id', '=', 'subject_teacher.teacher_id')
            ->where('subject_class.class_id', $klassId)
            ->where('periods.day', $day)
            ->select('periods.period_id', 'subjects.subject_code', 'subjects.name as subject_name', 'periods.duration', 'periods.period_num', 'periods.room', DB::raw('GROUP_CONCAT(teachers.name) teacher_names'), DB::raw('GROUP_CONCAT(teachers.teacher_id) teacher_ids'))
            ->groupby('periods.period_id')
            ->orderby('periods.period_num')
            ->get();
    }

    public static function getTeacherTimetable($teacher_id, $day) {
        return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('subject_class_teacher', 'subject_class_teacher.subject_class_id', '=', 'subject_class.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->where('subject_class_teacher.teacher_id', $teacher_id)
            ->where('periods.day', $day)
            ->select('subjects.*', 'periods.*')
            ->orderby('period_num')
            ->get();
    }

    public static function checkIfPeriodsAreTaughtByCurrentTeacher($period_ids) {
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

    public static function checkIfPeriodsAreOfSameSubjectAndClass($period_ids) {
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

    public static function getUniquePeriodNumber($period_ids) {
        return DB::table('periods')
            ->whereIn('period_id', $period_ids)
            ->select(DB::raw('count(distinct period_num) as number_of_periods'))
            ->first()->number_of_periods;
    }
}
