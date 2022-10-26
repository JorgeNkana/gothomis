<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_std_investigation_result extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','std_id','result','treated'];
}