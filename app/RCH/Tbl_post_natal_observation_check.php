<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_observation_check extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','observation_id','status'];
}