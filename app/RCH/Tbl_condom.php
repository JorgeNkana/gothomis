<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_condom extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['quantity','facility_id','user_id','place','patient_id'];
}