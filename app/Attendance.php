<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	public $timestamps = false;

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
}
