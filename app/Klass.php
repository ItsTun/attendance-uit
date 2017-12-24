<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Klass extends Model
{
	protected $table = "classes";
	protected $primaryKey = "class_id";

	public function students() {
		return $this->hasMany(Student::class, 'class_id');
	}

	public static function getClassFromPeriod($period_id) {
		return DB::table('periods')
            ->join('subjects', 'subjects.subject_id', '=', 'periods.subject_id')
            ->where('periods.period_id', $period_id)
            ->select('subjects.class_id')
            ->first();
	}
}
