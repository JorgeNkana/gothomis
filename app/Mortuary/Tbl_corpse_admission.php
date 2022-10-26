<?php

namespace App\mortuary;

use Illuminate\Database\Eloquent\Model;

class Tbl_corpse_admission extends Model
{
    //
	//use \App\UuidForKey; 
protected  $fillable=['admission_date','patient_id','corpse_id',
'admission_status_id','facility_id','user_id','mortuary_id','cabinet_id'
,'corpse_received_id','status','dept_id'];

}