<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_environmental_equipment_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected  $fillable=['user_id','facility_id','status','equipment_name','equipment_type_id'];
}