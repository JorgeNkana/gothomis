<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_family_planning_vvu_status extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','current_vvu_status','mother_vvu_status','partner_vvu_status'];
}