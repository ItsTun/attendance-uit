<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	public $timestamps = false;
	public $incrementing = false;

	protected $table = 'attendances';
	protected $primaryKey = 'student_roll_no';
	protected $fillable = ['student_roll_no', 'attendance_json'];

 	public function student() {
 		return $this->belongsTo('Student');
 	}

 	public static function updateStudentAttendance($rollNo, $studentAttendance) {
 		$attendance = Attendance::firstOrNew(array('student_roll_no' => $rollNo));
 		$attendance->attendance_json = $studentAttendance;
 		$attendance->save();
 	}

 	public static function show($rollNo) {
 		return Attendance::where('student_roll_no', '=', $rollNo)->first();
 	}
 	
 	public static function getAttendanceForSubject($class_id, $subject_id) {
 		$student = Student::getStudentsFromClass($class_id);
 		$roll_no = array_column($student->toArray(), 'roll_no');

 		$attendances = Attendance::whereIn('student_roll_no', $roll_no)->get();

 		$attendanceForSubject = [];

 		foreach($attendances as $attendance) {
 			$attendance_json = $attendance['attendance_json'];
 			$attendance_arr = json_decode($attendance_json);

 			foreach($attendance_arr as $key=>$value) {
 				if($subject_id != $value->subject_id) unset($attendance_arr[$key]);
 				else $attendanceForSubject[$attendance['student_roll_no']] = $value;
 			}
 		}

 		return $attendanceForSubject;
 	}
}
