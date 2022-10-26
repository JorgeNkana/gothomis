<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_family_planning_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','serial_no','facility_id','user_id','year','status','dob','occupation_id','residence_id','client_name','education'];
}