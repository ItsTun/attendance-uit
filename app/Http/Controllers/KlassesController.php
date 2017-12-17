<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Klass;
use App\Year;

class KlassesController extends Controller
{
    public function index() {
        $response = [];
    	$years = Year::all();

    	foreach ($years as $year) {
    		
    		$klasses = $year->klasses;

    		$info = [];

    		foreach ($klasses as $klass) {
    			
    			$data['class_id'] = $klass->class_id;
    			$data['name'] = $klass->name;
    			array_push($info, $data);

    		}

    		$response[$year->name] = $info;

    	}
    	return response($response);
    }
}
