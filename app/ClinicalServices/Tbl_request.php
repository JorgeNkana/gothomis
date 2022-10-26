<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_request extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['doctor_id','patient_id','eraser','visit_date_id','admission_id','requesting_department_id'];
}