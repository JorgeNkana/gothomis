<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_delivery_vvu_result extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','vvu_result','labour_delivery_result'];
}