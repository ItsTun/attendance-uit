<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Klass extends Model
{
	protected $table = "classes";
	protected $primaryKey = "class_id";

	public function students() {
		return $this->hasMany(Student::class, 'class_id');
	}
}
