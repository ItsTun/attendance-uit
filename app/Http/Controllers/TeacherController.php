<?php

namespace App\Http\Controllers;

class TeacherController extends Controller
{
    public function login() {
        return view('teacher.login');
    }

    public function dashboard() {
        return view('teacher.dashboard');
    }
}
