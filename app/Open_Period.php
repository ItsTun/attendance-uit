<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Open_Period extends Model
{
	public $timestamps = false;
	
	protected $table = "open_periods";
	protected $fillable = ['date', 'period_id'];
	protected $primaryKey = "open_period_id";

	public function attendedStudents() {
		return $this->hasMany(Period_Attendance::class, 'open_period_id');
	}

	public function period() {
		return $this->belongsTo(Period::class, 'period_id');
	}

	public static function fetch($periodId, $date) {
		$openPeriod = Open_Period::where('period_id', $periodId)
                                ->where('date', $date)
                                ->first();
        return $openPeriod;
	}

	public static function isOpen($class_id, $date) {
		return DB::table('open_periods')
					->join('periods', 'open_periods.period_id', '=', 'periods.period_id')
					->join('subject_class', 'periods.subject_class_id', '=', 'subject_class.subject_class_id')
					->join('classes', 'subject_class.class_id', '=', 'classes.class_id')
					->where('classes.class_id', $class_id)
					->where('open_periods.date', $date)
					->select(DB::raw('IF(count(open_periods.open_period_id) > 0, true, false) AS is_open'))
					->first();
	}
}
