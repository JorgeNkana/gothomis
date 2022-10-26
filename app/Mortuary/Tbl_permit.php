<?php

namespace App\Mortuary;

use Illuminate\Database\Eloquent\Model;

class Tbl_permit extends Model
{
	//use \App\UuidForKey; 
    //
       protected  $fillable=['facility_id','corpse_id','permission_status',
							 'descriptions','user_id','permit_number'];
}