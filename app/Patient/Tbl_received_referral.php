<?php

namespace App\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_received_referral extends Model
{
    //
	//use \App\UuidForKey; 
protected  $fillable=['patient_id','referring_facility_id','facility_id','visit_id','user_id'];
}