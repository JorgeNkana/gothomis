<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_ipdtreatment extends Model
{
    //
	//use \App\UuidForKey; 
    protected  $fillable=['item_id','deleted','admission_id','patient_id','remarks','timedosage','date_dosage','facility_id','user_id'];
}