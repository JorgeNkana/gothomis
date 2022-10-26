<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_postnatal_baby_feed_hour extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','baby_breastfeeding_within_hour'];

}