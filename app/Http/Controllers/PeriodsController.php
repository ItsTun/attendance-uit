<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Period;

class PeriodsController extends Controller
{
    public function show(Period $period) {

    	$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    	$periodStr = $days[$period->period_num].' '.$period->duration;

    	$response['period_str'] = $periodStr;

    	return response($response);

    }

    public function timetable(Request $request, $klassId) {

    	$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    	$day = $request->day;
    	$day = array_search($day, $days);

    	$periods = DB::select( DB::raw(
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

        foreach ($periods as $value) {
            
            $response[$value->period_num]['period_id'] = $value->period_id;
            $response[$value->period_num]['subject_code'] = $value->subject_code;
            $response[$value->period_num]['subject_name'] = $value->subject_name;
            $response[$value->period_num]['duration'] = $value->duration;
            $response[$value->period_num]['room'] = $value->room;
            $response[$value->period_num]['teacher_name'] = $value->teacher_name;

        }

    	return response($response);

    }

}
