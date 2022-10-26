<?php

namespace App\Pediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_pediatric_natal extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['client_id','facility_id','user_id','delivery_mode','delivery_place','baby_cry','apgar_score','others'];
}