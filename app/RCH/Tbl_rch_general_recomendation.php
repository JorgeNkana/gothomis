<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_rch_general_recomendation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','opinion','opinion_type'];
}