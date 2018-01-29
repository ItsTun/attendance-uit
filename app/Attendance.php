<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Subject_Class;

class Attendance extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'attendances';
    protected $primaryKey = 'student_id';
    protected $fillable = ['student_id', 'attendance_json'];

    public static function updateStudentAttendance($student_id, $studentAttendance)
    {
        $attendance = Attendance::firstOrNew(array('student_id' => $student_id));
        $attendance->attendance_json = $studentAttendance;
        $attendance->save();
    }

    public static function show($student_id)
    {
        return Attendance::where('student_id', '=', $student_id)->first();
    }

    public static function getAttendanceForSubject($class_id, $subject_id)
    {
        $student = Student::getStudentsFromClass($class_id);
        $subject_class = Subject_Class::where('subject_id', $subject_id)->where('class_id', $class_id)->first();
        $student_ids = array_column($student->toArray(), 'student_id');

        $attendances = Attendance::whereIn('student_id', $student_ids)->get();

        $attendanceForSubject = [];

        foreach ($attendances as $attendance) {
            $attendance_json = $attendance->attendance_json;
            $attendance_arr = json_decode($attendance_json);

            foreach ($attendance_arr as $key => $value) {
                if ($subject_class->subject_class_id != $value->subject_class_id) unset($attendance_arr[$key]);
                else {
                    $attendanceForSubject[$attendance->student->roll_no] = [];
                    $attendanceForSubject[$attendance->student->roll_no]['name'] = $attendance->student->name;
                    $attendanceForSubject[$attendance->student->roll_no]['percent'] = number_format($value->percent,2);
                }
            }
        }

        return $attendanceForSubject;
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}