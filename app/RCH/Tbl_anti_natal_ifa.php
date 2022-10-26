<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_ifa extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','ifa','quantity'];
}