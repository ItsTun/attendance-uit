<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    	$period = new Period();
        $results = $period->getTimetable($klassId, $day);

        foreach ($results as $value) {
            
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
