<?php

namespace App\Mortuary;

use Illuminate\Database\Eloquent\Model;

class Tbl_mortuary extends Model
{
	//use \App\UuidForKey; 
    //
	protected  $fillable=['mortuary_name','mortuary_class_id','facility_id','user_id'];
}