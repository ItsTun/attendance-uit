<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $table = "years";
    protected $primaryKey = "year_id";

    public function klasses() {
    	return $this->hasMany(Klass::class, 'year_id');
    }
}
