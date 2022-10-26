<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_fplanning_lab_investigation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','urine','albumin','sugar','others'];
}