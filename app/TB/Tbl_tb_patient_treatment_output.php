<?php

namespace App\TB;

use Illuminate\Database\Eloquent\Model;

class Tbl_tb_patient_treatment_output extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','client_id','output','comment'];
}