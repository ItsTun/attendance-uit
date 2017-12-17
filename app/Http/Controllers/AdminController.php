<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
    }

    public function teachers() {
        return view('admin.teachers');
    }

    public function classes() {
        return view('admin.classes');
    }

    public function subjects() {
        return view('admin.subjects');
    }

    public function timetables() {
        return view('admin.timetables');
    }

    public function students() {
        return view('admin.students');
    }

    public function attendance() {
        return view('admin.attendance');
    }

    public function addNewAdmin() {
        return view('admin.add_new');
    }

    public function years() {
        return view('admin.years');
    }


}
