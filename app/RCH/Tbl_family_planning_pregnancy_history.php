<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_family_planning_pregnancy_history extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','pregnancy_number','miscarriage_number','msb_number','alive_born_number','last_delivery_date','child_alive_number'];
}