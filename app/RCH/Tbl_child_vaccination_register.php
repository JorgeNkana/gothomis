<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_vaccination_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','vaccination_id','date','mother_id','place'];
}