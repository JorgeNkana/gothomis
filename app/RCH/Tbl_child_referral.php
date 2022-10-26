<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_referral extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','date','transfered_institution_id','reason','mother_id'];
}