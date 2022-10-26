<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_additional_medication extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','ferrous_sulphate','fs_quantity','folic_acid','fa_quantity','vitamin_a','other_medics'];
}