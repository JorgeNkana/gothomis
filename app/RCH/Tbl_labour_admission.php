<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_admission extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','admission_date','pregnancy_age','pregnancy_height'];
}