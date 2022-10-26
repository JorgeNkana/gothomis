<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_emonc_service extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','antibiotic_given','ergometrin','oxytocin','misoprostol',
        'inj_mg_sulfate','placenter_removed','mva','d_c','blood_transfusion'];
}