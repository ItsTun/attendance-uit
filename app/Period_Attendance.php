<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period_Attendance extends Model
{
	public $timestamps = false;

	protected $table = "period_attendance";
	protected $primaryKey = "period_attendance_id";

	// public function scopeIsPresent($query, $rollNo, $openPeriodId) {
	// 	return $query->where('roll_no', $rollNo)->where('open_period_id', $openPeriodId)->first()->present;
	// }
}
