<?php

namespace App\General_appointment;

use Illuminate\Database\Eloquent\Model;

class Tbl_general_appointment extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['user_id','facility_id','patient_id','appoint_date','dept_id','description','status'];
}