<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_tt_vaccination extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','user_id','facility_id','vaccination_id','vaccination_date','has_card'];
}