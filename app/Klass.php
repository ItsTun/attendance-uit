<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Year;
use App\Teacher;

class Klass extends Model
{
    protected $table = "classes";
    protected $primaryKey = "class_id";

    public static function getClasses()
    {
        return Klass::paginate(PaginationUtils::getDefaultPageSize());
    }

    public static function getClassesOfTeacher($teacher_id)
    {
        return DB::table('teachers')
            ->join('subject_class_teacher', 'subject_class_teacher.teacher_id', 'teachers.teacher_id')
            ->join('subject_class', 'subject_class.subject_class_id', 'subject_class_teacher.subject_class_id')
            ->join('classes', 'classes.class_id', 'subject_class.class_id')
            ->join('subjects', 'subjects.subject_id', 'subject_class.subject_id')
            ->join('years', 'classes.year_id', 'years.year_id')
            ->where('teachers.teacher_id', '=', $teacher_id)
            ->select('classes.*', 'years.name as year_name', 'subjects.subject_id', 'subjects.subject_code', 'subjects.name as subject_name')
            ->get();
    }

    public static function getClassFromPeriod($period_id)
    {
        return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->where('periods.period_id', $period_id)
            ->select('subject_class.class_id')
            ->first();
    }

    public static function getAssociatedClass($teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        $years = Year::all();
        $classTeacherOf = $teacher->class_teacher_of;
        $yearHeadOf = $teacher->year_head_of;
        $departmentHeadOf = $teacher->department_head_of;

        if (!is_null($teacher->is_principle) && $teacher->is_principle == 1) return $years;

        if (!is_null($classTeacherOf) || !is_null($yearHeadOf)) {
            foreach ($years as $year) {
                if (!is_null($classTeacherOf)) {
                    $flag = 0;
                    foreach ($year->klasses as $key => $klass) {
                        if (!is_null($klass->faculty_class) && sizeof($klass->faculty_class) > 0 && !self::isClassInDepartment($klass, $departmentHeadOf)) {
                            unset($year->klasses[$key]);
                        } else if ($klass->class_id != $classTeacherOf) {
                            unset($year->klasses[$key]);
                        } else {
                            $flag = 1;
                        }
                    }
                    if ($flag == 0 && !is_null($yearHeadOf) && $year->year_id != $yearHeadOf) {
                        unset($year);
                    }
                }
            }
            return $years;
        } else {
            return [];
        }
    }

    public static function isClassInDepartment($klass, $departmentId)
    {
        $faculty_ids = array_column($klass->faculty_class->toArray(), 'faculty_id');
        return in_array($departmentId, $faculty_ids);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subject_class()
    {
        return $this->hasMany(Subject_Class::class, 'class_id');
    }

    public function faculty_class()
    {
        return $this->hasMany(Faculty_Class::class, 'class_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}
