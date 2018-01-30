<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $table = "faculties";
    protected $primaryKey = "faculty_id";

    public function teachers() {
    	return $this->hasMany(Teacher::class, 'faculty_id');
    }

    public static function getFaculty() {
    	return Faculty::paginate(PaginationUtils::getDefaultPageSize());
    }
}
