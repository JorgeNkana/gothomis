<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_investigation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','user_id','facility_id','bp','hb'];
}