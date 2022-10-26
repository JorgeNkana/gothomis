<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_environmental_waste_collection extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','waste_type_id','waste_collected','equipment_used_id'];
}