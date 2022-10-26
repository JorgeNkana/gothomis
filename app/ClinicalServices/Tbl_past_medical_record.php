<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_medical_record extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['descriptions','status','past_medical_history_id',
        'past_surgical','other_past_medicals','surgeries','admissions','transfusion','immunisation'];
}