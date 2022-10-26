<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_birth_info extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','number_of_delivery','number_of_pregnancy','number_alive_children'];
}