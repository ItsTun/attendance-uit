<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;

class StudentsController extends Controller
{
    public function show(Student $student) {
    	return response($student);
    }

    public function findByEmail($email) {
    	$student = Student::getStudentByEmail($email);
    	if(!$student) {
    		return response('No student found with such email!');
    	}
        $s = $student->first();
        $response['roll_no'] = $s->roll_no;
        $response['name'] = $s->name;
        $response['email'] = $s->email;
        $response['class_short_form'] = $s->class_short_form;
        $response['class_name'] = $s->class_name;
    	return response($response);
    }
}
