<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;

class StudentsController extends Controller
{
    public function findByEmail($email) {
    	$student = Student::where('email', $email)->first();
    	if ($student == null) {
    		return response('No student found with such email!', 200);
    	}
        $response['student_id'] = $student->student_id;
        $response['roll_no'] = $student->roll_no;
        $response['name'] = $student->name;
        $response['email'] = $student->email;
        $response['class_id'] = $student->klass->class_id;
        $response['class_short_form'] = $student->klass->short_form;
        $response['class_name'] = $student->klass->name;
    	return response($response);
    }
}
