<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','serial_no','year','client_name','dob','weight','midwife',
        'delivery_place','mother_name','father_name','mobile_number','gender','residence_id'];
}