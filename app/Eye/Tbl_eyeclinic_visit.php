<?php

namespace App\Eye;

use Illuminate\Database\Eloquent\Model;

class Tbl_eyeclinic_visit extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['patient_id','visit_date_id','admission_id','facility_id','user_id'];
}