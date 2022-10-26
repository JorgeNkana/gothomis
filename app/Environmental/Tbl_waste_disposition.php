<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_waste_disposition extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','waste_disposed','waste_type_id','waste_disposal_type'];
}