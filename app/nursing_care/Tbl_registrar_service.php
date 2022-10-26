<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_registrar_service extends Model
{
    //service_id
	//use \App\UuidForKey; 
	  protected $fillable=['service_id','facility_id'];
}