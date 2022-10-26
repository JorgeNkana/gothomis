<?php

namespace App\TB;

use Illuminate\Database\Eloquent\Model;

class Tbl_patient_tb_type_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','facility_id','client_id','tb_type'];
}