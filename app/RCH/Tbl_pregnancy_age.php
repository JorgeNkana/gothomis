<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_pregnancy_age extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','user_id','facility_id','week'];
}