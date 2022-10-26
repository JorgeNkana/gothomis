<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_fplanning_cervix_cancer_investigation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','virginal_discharge','cervix_scratching',
        'cervix_swelling','virginal_bleeding','others'];
}