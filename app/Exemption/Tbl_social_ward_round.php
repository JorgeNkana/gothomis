<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_social_ward_round extends Model
{
    //
	//use \App\UuidForKey; 
    protected  $fillable=['user_id','patient_id','facility_id','issue_id','plan','output','remarks'];
}