<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_client_violence_output extends Model
{
	////use \App\UuidForKey; 
    //
    protected $fillable=['user_id','patient_id','facility_id','output_id'];
}