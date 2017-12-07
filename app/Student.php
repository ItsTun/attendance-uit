<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	
    protected $table = "students";
    protected $primaryKey = "roll_no";

    public function klass() {
    	return $this->belongsTo(Klass::class, 'class_id', 'class_id');
    }

    // public function scopeAttendedPeriods($query, $month) {
    // 	return $query->where('month');
    // }
}
