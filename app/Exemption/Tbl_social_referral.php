<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_social_referral extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','patient_id','facility_id','ref_type','facility_name'];
}