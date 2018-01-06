<?php

namespace App\Http\Controllers;

use App\Year;
use App\Klass;
use App\Subject_Class;
use App\Period;
use App\Subject;
use App\Student;

use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
    }

    public function teachers() {
        return view('admin.teachers');
    }

    public function classes() {
        $years = Year::all();

        $klasses = Klass::getClasses();

        return view('admin.classes')->with(['years' => $years, 'klasses' => $klasses]);
    }

    public function subjects() {
        $query = Input::get('q');

        $subjects = Subject::getSubjects($query);

        $years = Year::all();

        return view('admin.subjects')->with(['subjects' => $subjects, 'years' => $years, 'query' => $query]);
    }

    public function timetables() {
        $year_id = Input::get('year_id');
        $klass_id = Input::get('class_id');

        $years = Year::all();

        $subject_classes = Subject_Class::getSubjectsFromClass($klass_id);

        $subject_class_ids = Subject_Class::getSubjectClassIdsFromClass($klass_id);

        $lunch_break_subject_class_id = Subject_Class::getLunchBreakSubjectClassId($klass_id);

        $periods = Period::getPeriodsFromSubjectClass($subject_class_ids);

        return view('admin.timetables')->with(['years' => $years,
            'year_id' => $year_id,
            'class_id' => $klass_id,
            'subject_classes' => $subject_classes,
            'periods' => $periods,
            'lunch_break_subject_class_id' => $lunch_break_subject_class_id]);
    }

    public function students() {
        $years = Year::all();
        $query = Input::get('q');
        $roll_no = Input::get('r_q');
        $c_id = Input::get('c_id');

        $students = Student::getStudents($query, $roll_no, $c_id);

        return view('admin.students')->with(['students' => $students, 'years' => $years, 'name_query' => $query, 'roll_no_query' => $roll_no, 'class_id' => $c_id]);
    }

    public function attendance() {
        return view('admin.attendance');
    }

    public function addNewAdmin() {
        return view('admin.add_new');
    }

    public function years() {
        $years = Year::getYears();
        return view('admin.years')->with(['years' => $years]);
    }

    public function getStudentWithEmail() {
        $email = Input::get('email');
        $student = Student::where('email', $email)->first();

        return (is_null($student))?'null':$student;
    }

    public function getStudent() {
        $roll_no = Input::get('roll_no');
        $student = Student::find($roll_no);

        return (is_null($student))?'null':$student;
    }

    public function addOrUpdateStudent() {
        $prefix = Input::post('prefix');
        $roll_no = Input::post('roll_no');
        $name = Input::post('name');
        $email = Input::post('email');
        $class_id = Input::post('class_id');
        $old_roll_no = Input::post('old_r');

        $student = null;

        if(!is_null($old_roll_no)) $student = Student::find($old_roll_no);

        if(is_null($student)) {
            $student = new Student();
        }

        $student->roll_no = $prefix.''.$roll_no;
        $student->name = $name;
        $student->email = $email;
        $student->class_id = $class_id;
        $student->save();
    }

    public function addOrUpdateYear() {
        $name = Input::post('name');
        $year_id = Input::post('year_id');

        $year;

        if(!is_null($year_id)) {
            $year = Year::find($year_id);
        } else {
            $year = new Year();
        }

        $year->name = $name;
        $year->save();

        return back()->withInput();
    }

    public function addOrUpdateSubject() {
        $subjectCode = Input::post('subject_code');
        $name = Input::post('name');
        $klasses = Input::post('classes');
        $subjectId = Input::post('subject_id');

        $subject;

        if(!is_null($subjectId)) {
            $subject = Subject::find($subjectId);
        } else {
            $subject = new Subject();
        }

        $subject->subject_code = $subjectCode;
        $subject->name = $name;
        $subject->save();

        if(sizeof($subject->subject_class) > 0) foreach($subject->subject_class as $subject_class) {
            $existingClassId = $subject_class->klass->class_id;
            if(!in_array($existingClassId, $klasses)) $subject_class->delete();
            else unset($klasses[array_search($existingClassId, $klasses)]);
        }

        foreach($klasses  as $klass) {
            $subjectClass = new Subject_Class();
            $subjectClass->subject_id = $subject->subject_id;
            $subjectClass->class_id = $klass;
            $subjectClass->save();
        }

        return back()->withInput();
    }

    public function addOrUpdateClass() {
        $shortForm = Input::post('short_form');
        $name = Input::post('name');
        $year_id = Input::post('year_id');
        $class_id = Input::post('class_id');

        $klass;

        if(!is_null($class_id)) {
            $klass = Klass::find($class_id);
        } else {
            $klass = new Klass();
        }

        $klass->year_id = $year_id;
        $klass->short_form = $shortForm;
        $klass->name = $name;
        $klass->save();

        return back()->withInput();
    }

    public function addOrUpdatePeriods() {
        $periods = Input::post('periods');
        $class_id = Input::post('class_id');

        $lunchBreakSubjectClassId = Subject_Class::getLunchBreakSubjectClassId($class_id);
        
        foreach($periods as $period) {
            if(array_key_exists('is_lunch_break', $period) && $period['is_lunch_break'] == 1) {
                $periodTemp = Period::getPeriod($lunchBreakSubjectClassId->subject_class_id, $period['day'], $period['period_num']);
                if(is_null($periodTemp)) $periodTemp = new Period();
                $periodTemp->subject_class_id = $lunchBreakSubjectClassId->subject_class_id;
                $periodTemp->period_num = $period['period_num'];
                $periodTemp->day = $period['day'];
                $periodTemp->start_time = $period['start_time'];
                $periodTemp->end_time = $period['end_time'];
                $periodTemp->save();
            } else if(array_key_exists('subject_class_id', $period)) {
                $periodTemp = Period::getPeriod($period['subject_class_id'], $period['day'], $period['period_num']);
                if(is_null($periodTemp)) $periodTemp = new Period();
                $periodTemp->subject_class_id = $period['subject_class_id'];
                $periodTemp->room = $period['room'];
                $periodTemp->period_num = $period['period_num'];
                $periodTemp->day = $period['day'];
                $periodTemp->start_time = $period['start_time'];
                $periodTemp->end_time = $period['end_time'];
                $periodTemp->save();
            }
        }
    }


}
