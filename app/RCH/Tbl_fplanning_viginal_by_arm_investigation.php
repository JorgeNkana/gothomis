<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_fplanning_viginal_by_arm_investigation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','placenta_size','placenta_layout','adnexa','others'];
}