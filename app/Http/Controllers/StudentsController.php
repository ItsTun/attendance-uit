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
    	$student = Student::email($email)->first();
    	if(!$student) {
    		return response('No student found with such email!');
    	}
    	return response($student);
    }
}
