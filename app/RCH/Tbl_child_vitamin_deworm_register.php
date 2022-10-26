<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_vitamin_deworm_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','deworm_given','vitamin_given','date_attended'];
}