<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = "subjects";
    protected $primaryKey = "subject_id";

    public function periods() {
 		return $this->hasMany(Period::class, 'subject_id');
    }
}
