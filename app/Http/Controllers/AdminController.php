<?php

namespace App\Http\Controllers;

use App\Year;
use App\Klass;
use App\Subject_Class;
use App\Subject_Class_Teacher;
use App\Period;
use App\Subject;
use App\Student;
use App\Teacher;
use App\Faculty;
use App\Utils;
use App\Open_Period;
use App\Period_Attendance;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;
use DateTime;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function teachers()
    {
        $query = Input::get('q');
        $f_id = Input::get('f_id');
        $faculties = Faculty::all();
        $teachers = Teacher::getTeachers($query, $f_id);
        $years = Year::all();

        return view('admin.teachers')->with(['teachers' => $teachers, 'faculties' => $faculties, 'years' => $years, 'query' => $query, 'faculty_id' => $f_id]);
    }

    public function migrationTool()
    {
        $years = Year::all();

        return view('admin.migration-tool')->with(['years' => $years]);
    }

    public function classes()
    {
        $years = Year::all();

        $klasses = Klass::getClasses();

        return view('admin.classes')->with(['years' => $years, 'klasses' => $klasses]);
    }

    public function subjects()
    {
        $query = Input::get('q');

        $subjects = Subject::getSubjects($query);

        $years = Year::all();

        return view('admin.subjects')->with(['subjects' => $subjects, 'years' => $years, 'query' => $query]);
    }

    public function timetables()
    {
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

    public function students()
    {
        $years = Year::all();
        $query = Input::get('q');
        $roll_no = Input::get('r_q');
        $c_id = Input::get('c_id');

        $students = Student::getStudents($query, $roll_no, $c_id);

        return view('admin.students')->with(['students' => $students, 'years' => $years, 'name_query' => $query, 'roll_no_query' => $roll_no, 'class_id' => $c_id]);
    }

    public function getStudentsFromClass()
    {
        $class_id = Input::get('class_id');

        $students = Student::getStudentsFromClass($class_id);

        return $students;
    }

    public function getTeacherWithEmail()
    {
        $email = Input::get('email');

        $teacher = Teacher::where('email', $email)->first();

        return (is_null($teacher)) ? 'null' : $teacher;
    }

    public function studentsCsv()
    {
        $classes_ary = $this->getClasses();
        return view('admin.studentscsv')->with(['classes_ary' => $classes_ary]);
    }

    function getClasses()
    {
        $response = [];
        $years = Year::all();
        foreach ($years as $year) {
            $klasses = $year->klasses;
            $info = [];

            foreach ($klasses as $klass) {
                $data['class_id'] = $klass->class_id;
                $data['name'] = $klass->name;
                $data['short_form'] = $klass->short_form;
                array_push($info, $data);
            }

            $response[$year->name] = $info;
        }
        return $response;
    }

    public function getArrayFromCSV(Request $request)
    {
        $file = $request->file('students_csv');
        $validator = Validator::make(
            [
                'file' => $file,
                'extension' => strtolower($file->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:csv',
            ]
        );

        if ($validator->fails()) {
            return "Only csv files are allowed!";
        }

        $stundentAry = $this->studentCsvToArray($file);
        return $stundentAry;
    }

    function studentCsvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (count($row) != 3) return false;

                if (!$header) {
                    $header = $row;
                    if ($header[0] != 'roll_no' || $header[1] != 'name' || $header[2] != 'email')
                        return false;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }

    public function saveStudentsFromCSV(Request $request)
    {
        $students = json_decode($request->students);
        foreach ($students as $value) {
            $student = new Student();
            $student->roll_no = $value->roll_no;
            $student->name = $value->name;
            $student->email = $value->email;
            $student->class_id = $value->class_id;
            $student->save();
        }
        echo "Students saved!";
    }

    public function checkIfEmailExists(Request $request)
    {
        $emails = json_decode($request->emails);
        $r_emails = [];

        $students = Student::getStudentsWithEmails($emails);
        foreach ($students as $value) {
            array_push($r_emails, $value->email);
        }

        $teachers = Teacher::getTeachersWithEmails($emails);
        foreach ($teachers as $value) {
            if (!in_array($value->email, $r_emails)) {
                array_push($r_emails, $value->email);
            }
        }

        return response($r_emails);
    }

    public function checkIfRollNoExists(Request $request)
    {
        $roll_nos = json_decode($request->roll_nos);
        $students = Student::getStudentsWithRollNos($roll_nos);
        $r_roll_nos = [];
        foreach ($students as $value) {
            array_push($r_roll_nos, $value->roll_no);
        }
        return response($r_roll_nos);
    }

    public function studentsAttendanceDetails()
    {
        $classes_ary = $this->getClasses();
        return view('admin.student_attendance_details')->with(['classes_ary' => $classes_ary]);
    }

    public function getStudentAttendanceDetails(Request $request)
    {
        $roll_no = $request->roll_no;
        $from = $request->from;
        $to = $request->to;

        $student = Student::where('roll_no', $roll_no)->first();
        if (!$student) {
            return response(null, '204');
        }
        $class = Student::where('roll_no', $roll_no)->first()->klass;
        $class_id = $class->class_id;

        $result = Period_Attendance::getAttendanceDetails($roll_no, $from, $to);

        $response = [];
        $last_date = '';
        $attendances = [];
        foreach ($result as $value) {
            if ($last_date != $value->date) {
                $attendances = [];
                $last_date = $value->date;
                array_push($response, []);
            }

            $data = new \stdClass();
            $data->subject_code = $value->subject_code;
            $data->present = $value->present;
            $data->period_num = $value->period_num;
            $attendances[$value->period_num] = $data;

            $response[count($response) - 1]['date'] = $value->date;
            $response[count($response) - 1]['attendances'] = $attendances;
        }

        $timetables = [];
        foreach ($response as $key => $value) {
            $date = $value['date'];
            $attendances = $value['attendances'];
            $day = Utils::getDayFromDate($date);

            if (!array_key_exists($day, $timetables)) {
                $timetable = Period::getTimetable($class_id, $day);
                $timetables[$day] = $timetable->toArray();
            }

            foreach ($timetables[$day] as $timetable) {
                if (!array_key_exists($timetable->period_num, $value['attendances'])) {
                    $data = new \stdClass();
                    $data->subject_code = $timetable->subject_code;
                    $data->period_num = $timetable->period_num;
                    $data->present = -1;
                    $response[$key]['attendances'][$timetable->period_num] = $data;
                }
            }
        }

        return response(json_encode($response), '200');
    }

    public function studentsAbsentList()
    {
        $classes_ary = $this->getClasses();
        return view('admin.students_absent_list')->with(['classes_ary' => $classes_ary]);
    }

    public function getStudentsAbsentList(Request $request)
    {
        $class_id = $request->class_id;
        $from = $request->from;
        $to = $request->to;

        $absent_list = Period_Attendance::getStudentsAbsentList($class_id, $from, $to);
        $list = [];
        foreach ($absent_list as $value) {
            $list[$value->date] = $value->absent_students;
        }

        $response = [];
        $date = new DateTime($from);
        $end_date = new DateTime($to);
        while ($date <= $end_date) {
            $data = [];
            $data['date'] = $date->format('Y-m-d');
            if (array_key_exists($date->format('Y-m-d'), $list)) {
                $data['absent_students'] = $list[$date->format('Y-m-d')];
                array_push($response, $data);
            } else {
                $day = Utils::getDayFromDate($date->format('Y-m-d'));
                if ($day != 0 && $day != 6) {
                    $is_open = Open_Period::isOpen($class_id, $date->format('Y-m-d'))->is_open;
                    if ($is_open == 1) {
                        $data['absent_students'] = null;
                        array_push($response, $data);
                    }
                }
            }
            $date->modify('+1 day');
        }
        return response(json_encode($response), '200');
    }

    public function batchUpdate()
    {
        $years = Year::all();
        $year_id = Input::get('year_id');
        $klass_id = Input::get('class_id');
        $students = [];
        if (!is_null($year_id) && !is_null($klass_id)) $students = Student::getStudentsFromYearOrClass($year_id, $klass_id);
        return view('admin.student_batch_update')->with(['years' => $years, 'year_id' => $year_id, 'class_id' => $klass_id, 'students' => $students]);
    }

    public function attendance()
    {
        $period = new Period();
        $date = Input::get('date');
        $msgCode = Input::get('msg_code');
        $class_id = Input::get('class_id');

        if (!is_null($date) && !Utils::validateDate($date)) {
            return "Invalid date format!";
        }

        $years = Year::all();

        $currentDay = date('N');
        $timetable = $period->getTimetableInDay((!is_null($date)) ? Utils::getDayFromDate($date) : (($currentDay != 6 && $currentDay != 7) ? $currentDay : 1), $class_id);

        $with = ['timetables' => $timetable, 'dates' => Utils::getDatesInThisWeek()];
        $with['selectedDate'] = (!is_null($date)) ? $date : Utils::getDefaultDate();
        $with['msgCode'] = (!is_null($msgCode)) ? $msgCode : 0;
        $with['class_id'] = (!is_null($class_id)) ? $class_id : '';
        $with['years'] = $years;

        return view('admin.attendance')->with($with);
    }

    public function addNewAdmin()
    {
        return view('admin.add_new');
    }

    public function years()
    {
        $years = Year::getYears();
        return view('admin.years')->with(['years' => $years]);
    }

    public function getStudentWithEmail()
    {
        $email = Input::get('email');
        $student = Student::where('email', $email)->first();

        return (is_null($student)) ? 'null' : $student;
    }

    public function getStudent()
    {
        $roll_no = Input::get('roll_no');
        $student = Student::where('roll_no', $roll_no)->first();

        return (is_null($student)) ? 'null' : $student;
    }

    public function addOrUpdateTeacher()
    {
        $name = Input::post('name');
        $email = Input::post('email');
        $faculty_id = Input::post('faculty_id');
        $subject_classes = Input::post('subject_classes');
        $teacher_id = Input::post('teacher_id');
        $class_teacher_of = Input::post('class_teacher_of');
        $year_head_of = Input::post('year_head_of');
        $is_principle = Input::post('is_principle');

        $teacher = null;

        if (!is_null($teacher_id)) $teacher = Teacher::find($teacher_id);
        if (is_null($teacher)) $teacher = new Teacher();

        $teacher->name = $name;
        $teacher->email = $email;
        $teacher->faculty_id = $faculty_id;
        $teacher->class_teacher_of = (is_null($class_teacher_of)) ? null : $class_teacher_of;
        $teacher->year_head_of = (is_null($year_head_of)) ? null : $year_head_of;
        $teacher->is_principle = (is_null($is_principle)) ? null : 1;
        $teacher->save();

        $teacher_id = $teacher->teacher_id;

        if (sizeof($teacher->subject_teachers) > 0) foreach ($teacher->subject_teachers as $subject_teacher) {
            $existingSubjectClass = $subject_teacher->subject_class->subject_class_id;
            if (!in_array($existingSubjectClass, $subject_classes)) {
                Subject_Class_Teacher::deleteRecord($existingSubjectClass, $teacher_id);
            } else unset($subject_classes[array_search($existingSubjectClass, $subject_classes)]);
        }

        foreach ($subject_classes as $subject_class) {
            $subject_class_teacher = new Subject_Class_Teacher();
            $subject_class_teacher->subject_class_id = $subject_class;
            $subject_class_teacher->teacher_id = $teacher_id;
            $subject_class_teacher->save();
        }

        return back()->withInput();
    }

    public function addOrUpdateStudent()
    {
        $prefix = Input::post('prefix');
        $student_id = Input::post('student_id');
        $roll_no = Input::post('roll_no');
        $name = Input::post('name');
        $email = Input::post('email');
        $class_id = Input::post('class_id');
        $old_roll_no = Input::post('old_r');

        $student = null;

        if (!is_null($student_id)) $student = Student::find($student_id);

        if (is_null($student)) {
            $student = new Student();
        }

        $student->roll_no = $prefix . '-' . $roll_no;
        $student->name = $name;
        $student->email = $email;
        $student->class_id = $class_id;
        $student->save();
    }

    public function addOrUpdateYear()
    {
        $name = Input::post('name');
        $year_id = Input::post('year_id');

        $year;

        if (!is_null($year_id)) {
            $year = Year::find($year_id);
        } else {
            $year = new Year();
        }

        $year->name = $name;
        $year->save();

        return back()->withInput();
    }

    public function addOrUpdateSubject()
    {
        $subjectCode = Input::post('subject_code');
        $name = Input::post('name');
        $klasses = Input::post('classes');
        $subjectId = Input::post('subject_id');

        $subject;

        if (!is_null($subjectId)) {
            $subject = Subject::find($subjectId);
        } else {
            $subject = new Subject();
        }

        $subject->subject_code = $subjectCode;
        $subject->name = $name;
        $subject->save();

        if (sizeof($subject->subject_class) > 0) foreach ($subject->subject_class as $subject_class) {
            $existingClassId = $subject_class->klass->class_id;
            if (!in_array($existingClassId, $klasses)) $subject_class->delete();
            else unset($klasses[array_search($existingClassId, $klasses)]);
        }

        foreach ($klasses as $klass) {
            $subjectClass = new Subject_Class();
            $subjectClass->subject_id = $subject->subject_id;
            $subjectClass->class_id = $klass;
            $subjectClass->save();
        }

        return back()->withInput();
    }

    public function addOrUpdateClass()
    {
        $shortForm = Input::post('short_form');
        $name = Input::post('name');
        $year_id = Input::post('year_id');
        $class_id = Input::post('class_id');

        $klass;

        if (!is_null($class_id)) {
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

    public function addOrUpdatePeriods()
    {
        $periods = Input::post('periods');
        $class_id = Input::post('class_id');

        $lunchBreakSubjectClassId = Subject_Class::getLunchBreakSubjectClassId($class_id);

        foreach ($periods as $period) {
            if (array_key_exists('is_lunch_break', $period) && $period['is_lunch_break'] == 1) {
                $periodTemp = Period::getPeriod($lunchBreakSubjectClassId->subject_class_id, $period['day'], $period['period_num']);
                if (is_null($periodTemp)) $periodTemp = new Period();
                $periodTemp->subject_class_id = $lunchBreakSubjectClassId->subject_class_id;
                $periodTemp->period_num = $period['period_num'];
                $periodTemp->day = $period['day'];
                $periodTemp->start_time = $period['start_time'];
                $periodTemp->end_time = $period['end_time'];
                $periodTemp->save();
            } else if (array_key_exists('subject_class_id', $period)) {
                $periodTemp = ($period['period_id'] != -1) ? Period::find($period['period_id']) : new Period();
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

    public function addAttendance($period_ids)
    {
        $date = Input::get('date');
        $periods = explode(',', $period_ids);
        $error = $this->check($date, $periods);
        $periodObjects = Period::find($periods);
        $numberOfPeriods = Period::getUniquePeriodNumber($periods);
        if (is_null($error)) {
            $students = Student::getStudentsFromPeriod($periods);
            return view('admin.add_attendance')->with(['students' => $students, 'periods' => $periods, 'date' => $date,
                'periodObjects' => $periodObjects, 'numberOfPeriods' => $numberOfPeriods, 'attendedStudents' => $this->getAttendedStudentsFromPeriods($periods, $date)]);
        } else {
            return $error;
        }
    }

    private function check($date, $periods)
    {
        if (Utils::validateDate($date)) {
            foreach ($periods as $period) {
                if (Utils::periodIsInDate($periods, $date)) {
                    return null;
                } else {
                    return "There is no period with id $period in $date";
                }
            }
        } else {
            return "Invalid date format!";
        }
    }

    private function getAttendedStudentsFromPeriods($period_ids, $date)
    {
        $attendedStudents = null;
        $students = null;
        foreach ($period_ids as $period_id) {
            $openPeriod = Open_Period::fetch($period_id, $date);
            if (!is_null($openPeriod)) $students = $openPeriod->attendedStudents;
            if (!is_null($students)) {
                if (is_null($attendedStudents)) $attendedStudents = [];
                $attendedStudents[$period_id . '_student'] = $students;
            } else {
                if (!is_null($attendedStudents)) $attendedStudents[$period_id . '_student'] = [];
            }
        }
        return $attendedStudents;
    }

    public function saveOrEditAttendance()
    {
        $date = Input::get('date');
        $presentStudents = [];
        $periods = Input::get('period');

        $period_ids = explode(',', $periods);

        $error = $this->check($date, $period_ids);

        if (is_null($error)) {
            foreach ($period_ids as $period_id) {
                $key = $period_id . '_student';
                $students = Input::post($key);
                $presentStudents[$key] = (is_null($students)) ? [] : $students;
            }

            $isUpdate = !is_null($this->getAttendedStudentsFromPeriods($period_ids, $date));

            if (!$isUpdate) {
                Period_Attendance::saveAttendance($period_ids, $date, $presentStudents);
            } else {
                Period_Attendance::updateAttendance($period_ids, $date, $presentStudents);
            }

            return redirect()->action('AdminController@attendance', ['msg_code' => ($isUpdate) ? '2' : '1']);
        } else {
            return $error;
        }
    }

    public function migrateStudents()
    {
        $class_id = Input::get('to_class_id');
        $students = Input::get('students');

        $klass = Klass::find($class_id);

        foreach ($students as $stu) {
            $student = Student::find($stu['student_id']);
            $student->class_id = $class_id;
            $student->roll_no = $klass->short_form . $stu['new_roll_no'];
            $student->save();
        }

        return "Save successfully";
    }


}
