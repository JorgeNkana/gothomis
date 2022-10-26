<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_fplanning_previous_pregnancy_result extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','year','delivery_method','delivery_place','delivery_results',
    'baby_feeding'];
}