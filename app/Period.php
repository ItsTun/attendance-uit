<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Subject_Class;

class Period extends Model
{
    public $timestamps = false;
    
    protected $table = "periods";
    protected $primaryKey = "period_id";

    public function subject_class() {
    	return $this->belongsTo(Subject_Class::class, 'subject_class_id');
    }
    
    public function subject() {
    	$subjectClass = Subject_Class::find($this->subject_class_id)->first();
        return $subjectClass->subject;
    }

    public static function getPeriodsFromSubjectClass($subject_class_ids) {
        return Period::whereIn('subject_class_id', $subject_class_ids)->get();
    }

    public static function getPeriod($subject_class_id, $day, $period_num) {
        return Period::where('subject_class_id', $subject_class_id)->where('day', $day)->where('period_num', $period_num)->first();
    }

    public static function getTimetable($day, $klassId) {
        $subject_classes = Subject_Class::where('class_id', $klassId)->get();
        $periods = Period::where('day', $day)->orWhereIn('subject_class_id', $subject_classes)->whereNull('subject_class_id')->orderby('period_num')->get();
        return $periods;
    }

    public static function getTimetableInDay($day, $class_id) {
        $sql = DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id')
            ->join('classes', 'classes.class_id', '=', 'subject_class.class_id')
            ->where('periods.day', $day)
            ->select('periods.period_id', 'subjects.subject_code', 'subjects.name as subject_name', 'subject_class.subject_class_id','periods.start_time', 'periods.end_time','periods.period_num', 'periods.room', 'classes.short_form as class_short_form', 'classes.name as class_name')
            ->groupby('periods.period_id')
            ->orderby('periods.period_num')
            ->orderby('periods.subject_class_id');
        if(!is_null($class_id) && $class_id!=-1) $sql->where('subject_class.class_id', $class_id);
        return $sql->get();
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
