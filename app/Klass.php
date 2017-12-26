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

	public function subject_class() {
		return $this->hasMany(Subject_Class::class, 'class_id');
	}

	public static function getClassFromPeriod($period_id) {
		return DB::table('periods')
            ->join('subject_class', 'subject_class.subject_class_id', '=', 'periods.subject_class_id')
            ->where('periods.period_id', $period_id)
            ->select('subject_class.class_id')
            ->first();
	}
}
