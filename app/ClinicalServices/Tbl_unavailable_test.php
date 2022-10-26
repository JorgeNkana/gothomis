<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_unavailable_test extends Model
{
	//use \App\UuidForKey; 
    protected $fillable =['patient_id','user_id','visit_date_id','item_id','facility_id'];
}