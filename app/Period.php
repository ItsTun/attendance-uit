<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = "periods";
    protected $primaryKey = "period_id";

    public function subject() {
    	return $this->belongsTo(Subject::class, 'subject_id');
    }
}
