<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_gbv_vac extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','patient_id','referral_id','violence_category_id','violence_type_id','attachment_id',
    'followup_date','description','other_description','referral_reason','date_of_event','facility_id','service'];
}