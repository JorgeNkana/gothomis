<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_client_violence extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['user_id','patient_id','facility_id','violence_type_id','sub_violence_id','violence_category_id',
    'event_date'];
}