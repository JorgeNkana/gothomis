<?php

namespace App\Pediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_pediatric_diatary extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['client_id','facility_id','user_id','food_intake_quantity','food_intake_quality','others'];
}