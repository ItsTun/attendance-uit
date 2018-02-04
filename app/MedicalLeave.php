<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalLeave extends Model
{
    public $timestamps = true;
    public $incrementing = true;

    protected $table = 'medical_leaves';
    protected $primaryKey = 'medical_leave_id';
    protected $fillable = ['roll_no', 'leave_from', 'leave_to', 'added_by'];

}
