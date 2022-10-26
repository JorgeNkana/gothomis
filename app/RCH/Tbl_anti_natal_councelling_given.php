<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_councelling_given extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','description_id','status'];
}