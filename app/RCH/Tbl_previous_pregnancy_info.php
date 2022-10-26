<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_previous_pregnancy_info extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','number_of_pregnancy','number_of_delivery',
    'number_alive_children','number_of_miscarriage','year','lnmp','edd','delivery_place'];
}