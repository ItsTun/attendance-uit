<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Faculty;

/**
* Faculty Class model
*/
class Faculty_Class extends Model
{
	protected $table = "faculty_class";
	protected $primaryKey = "faculty_class_id";

	public function faculty() {
		return $this->belongsTo(Faculty::class, 'faculty_id');
	}

	public function klass() {
    	return $this->belongsTo(Klass::class, 'class_id');
    }
}