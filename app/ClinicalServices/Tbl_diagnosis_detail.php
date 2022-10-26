<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_diagnosis_detail extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['diagnosis_description_id','status','diagnosis_id'];

    public function tbl_diagnoses()
    {
        return $this->belongsTo('App\ClinicalServices\Tbl_diagnosis');
    }
}