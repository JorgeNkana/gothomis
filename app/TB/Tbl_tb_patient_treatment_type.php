<?php

namespace App\TB;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_patient_treatment_type extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','client_id','tb_treatment_type_id','treatment_place'];
}