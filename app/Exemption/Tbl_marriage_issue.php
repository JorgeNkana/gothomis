<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_marriage_issue extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','patient_id','facility_id','status','social_description','complainer_description','complainee_description','event_date'];
}