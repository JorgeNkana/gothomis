<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_admission extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['account_id','admission_date','patient_id','admission_status_id','facility_id','user_id','discharge_summary',"discharged_by"];
}