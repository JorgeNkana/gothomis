<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_fplanning_previous_menstral extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','lnmp','menstral_day','bleeding_quantity','menstral_cycle','pain'];
}