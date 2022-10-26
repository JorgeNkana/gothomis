<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_patient_emergence extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','registered_by','date_attended','facility_id'];
}