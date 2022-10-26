<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_fplanning_stomach_leg_investigation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','liver_inflammation','leg_inflammation','vericose_vein','others'];
}