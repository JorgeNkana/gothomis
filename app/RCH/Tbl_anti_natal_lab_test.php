<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_lab_test extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','blood_group','rh','vdrl_rpr','pmtct','mrdt_bs',
    'result','other_test'];
}