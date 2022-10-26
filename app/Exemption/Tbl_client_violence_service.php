<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_client_violence_service extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['user_id','patient_id','facility_id','service_id'];
}