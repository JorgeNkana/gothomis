<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_status_procedure extends Model
{
	//use \App\UuidForKey; 
      //
    protected  $fillable=['item_id','remarks','admission_id','patient_id','operation_date','status','facility_id','user_id'];

}