<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_deworm_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','deworm_given','vitamin_given','date'];
}