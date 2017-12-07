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
    		'SELECT periods.period_id, subjects.subject_code, subjects.name, periods.duration, periods.period_num
			FROM periods, subjects
			WHERE subjects.class_id = :class_id AND periods.subject_id = subjects.subject_id AND periods.day = :day
			ORDER BY periods.period_num;'
    	), array('day' => $day, 'class_id' => $klassId) );

    	return response($periods);

    }

}
