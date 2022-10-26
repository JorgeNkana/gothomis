<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_exemption_tracking_status extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','user_id','exemption_status','facility_id','date_created'];
}