<?php

namespace App\Nutrition;

use Illuminate\Database\Eloquent\Model;

class Tbl_client_nutritional_status extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','user_id','facility_id','visit_id','status','description','transfer','transfer_to'];
}