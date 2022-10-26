<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_icu_entry extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable = ['admission_id','doctor_id','date_admitted','source','icu_status_id'];
}