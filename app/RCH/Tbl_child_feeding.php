<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_feeding extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','feeding_type','mother_id'];
}