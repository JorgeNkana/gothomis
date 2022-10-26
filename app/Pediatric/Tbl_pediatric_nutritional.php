<?php

namespace App\Pediatric;

use Illuminate\Database\Eloquent\Model;

class Tbl_pediatric_nutritional extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['client_id','facility_id','user_id','muac','whz_score','others'];
}