<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Teacher extends Model
{
    protected $table = "teachers";
    protected $primaryKey = "teacher_id";

    public function subject_teachers() {
    	return $this->hasMany(Subject_Teacher::class, 'teacher_id');
    }
    
    public function getteacher($faculty) {
        return DB::table('teachers') 
            ->join('subject_teacher', 'subject_teacher.teacher_id', '=', 'teachers.teacher_id') 
            ->join('subjects', 'subjects.subjects_id', '=', 'subject_teacher.subject_id') 
            ->where('teachers.faculty', $faculty)
            ->select('teachers.*','subjects.name')
            ->groupby('faculty')
            ->orderby('teacher_id')
            ->get();
    }
    
    public function addteacher($name, $email, $faculty_id, $subj_id) {
        $result = DB::insert( DB::raw (
            'INSERT INTO teachers (name, email, faculty_id, s.subject_id) 
            VALUE (:name, :email, :faculty_id, :subj_id)
            FROM teachers INNER JOIN subject_teacher s 
            ON teachers.teacher_id=s.teacher_id'
        ),array('name' => $name, 'email' => $email, 'faculty_id' => $faculty_id, 'subj_id' => $subj_id) );
        
        return $result;
    }
    
    public function updateteacher($teacher_id, $name, $email, $faculty_id, $subj_id) {
        $result = DB::update( DB::raw (
            'UPDATE teachers,subject_teacher 
            SET teachers.name= :name, teachers.email= :email, teachers.faculty_id= :faculty_id, subject_teacher.subject_id= :subj_id 
            FROM teachers t, subject_teacher s 
            WHERE t.teacher_id=s.teacher_id 
            AND t.teacher_id=:teacher_id'
        ), array('teacher_id' => $teacher_id, 'name' => $name, 'email' => $email, 'faculty_id' => $faculty_id, 'subj_id' => $subj_id) );
        
        return $result;
    }
    
    public function deleteteacher($teacher_id) {
        $result = DB::delete( DB::raw (
            'DELETE t.*, s.*  
            FROM teachers t 
            INNER JOIN subject_teacher s ON t.teacher_id = s.teacher_id 
            WHERE t.teacher_id = :teacher_id'
        ), array('teacher_id' => $teacher_id) );
    }
}
