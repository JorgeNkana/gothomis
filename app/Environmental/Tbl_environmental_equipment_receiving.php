<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_environmental_equipment_receiving extends Model
{
    //
	//use \App\UuidForKey; 
    protected  $fillable=['user_id','facility_id','quantity','equipment_id','status','status_received','issued_quantity'];
}