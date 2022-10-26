<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_patient_procedure extends Model
{
	//use \App\UuidForKey; 
    protected $fillable =['item_id','patient_id','visit_date_id','user_id','admission_id','status'];
}