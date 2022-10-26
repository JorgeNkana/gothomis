<?php

namespace App\Environmental;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_rabies_registry extends Model
{
    //
	//use \App\UuidForKey; 
    protected  $fillable=['user_id','facility_id','vaccination_id','patient_id','vacc_type','dose_type'];
}