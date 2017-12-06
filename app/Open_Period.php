<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Open_Period extends Model
{
	public $timestamps = false;
	
	protected $table = "open_periods";
	protected $fillable = ['date', 'period_id'];
	protected $primaryKey = "open_period_id";

	public function attendStudents() {
		return $this->hasMany(Period_Attendance::class, 'open_period_id');
	}

	public function period() {
		return $this->belongsTo(Period::class, 'period_id');
	}
}
