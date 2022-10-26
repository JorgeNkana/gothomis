<?php

namespace App\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_exemption_number extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['patient_id','exemption_number','facility_id','user_id'];
}