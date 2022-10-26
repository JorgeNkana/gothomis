<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_diagnosis extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['department_id','patient_id','visit_date_id','user_id','facility_id','admission_id'];

    public function tbl_diagnosis_details()
    {
        return $this->hasMany('App\ClinicalServices\Tbl_diagnosis_detail');
    }
}