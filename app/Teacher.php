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
    
    public function getallteacher(){
        return DB::table('teacher')
            ->join('subject_class_teacher', 'subject_class_teacher.teacher_id', '=', 'teachers.teacher_id') 
            ->join('subjects_class', 'subjects_class.subject_id', '=', 'subject_class_teacher.subject_id') 
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id') 
            ->select('teachers.*','subjects.name as subj_name')
            ->groupby('faculty_id')
            ->orderby('teacher_id')
            ->get();
    }
    
    public function getteacher($faculty_id) {
        return DB::table('teachers') 
            ->join('subject_class_teacher', 'subject_class_teacher.teacher_id', '=', 'teachers.teacher_id') 
            ->join('subjects_class', 'subjects_class.subject_id', '=', 'subject_class_teacher.subject_id') 
            ->join('subjects', 'subjects.subject_id', '=', 'subject_class.subject_id') 
            ->where('teachers.faculty_id', $faculty_id) 
            ->select('teachers.*','subjects.name as subj_name') 
            ->groupby('faculty_id')
            ->orderby('teacher_id')
            ->get();
    }
    
    public function addteacher($name, $email, $faculty_id, $subject_class_ids) {
        $teacher = new Teacher();
        $teacher->name = $name;
        $teacher->email = $email;
        $teacher->faculty_id = $faculty_id;
        $teacher->save();
        
        $teacher = Teacher::where('email', $email)->first();
        $teacher_id = $teacher->teacher_id;
        
        foreach ($subject_class_ids as $subject_class_id) {
            $subject_teacher = new Subject_Teacher();
            $subject_teacher->subject_class_id = $subject_class_id;
            $subject_teacher->teacher_id = $teacher_id;
            $subject_teacher->save();
        }
        
                                  
    }
    
    public function updateteacher($teacher_id, $name, $email, $faculty_id, $subject_class_ids) {
        DB::table('teachers')
            ->whereIn('teacher_id', $teacher_id) 
            ->update(['name' => $name], ['email' => $email], ['faculty_id' => $faculty_id]);
        
        foreach ($subject_class_ids as $subject_class_id){
        DB::table('subject_class_teacher')
            ->whereIn('teacher_id',$teacher_id ) 
            ->update(['subject_class_id' => $subject_class_id])
        }
    }
        
    }
}
