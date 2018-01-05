<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{

	public $timestamps = false;

    protected $table = "years";
    protected $primaryKey = "year_id";

    public function klasses() {
    	return $this->hasMany(Klass::class, 'year_id');
    }

  	public static function getYears() {
  		return Year::paginate(PaginationUtils::getDefaultPageSize());
  	}
}
