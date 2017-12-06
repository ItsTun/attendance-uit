<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	protected $table = "attendances";

 	public function student() {
 		return $this->belongsTo('Student');
 	}
}
