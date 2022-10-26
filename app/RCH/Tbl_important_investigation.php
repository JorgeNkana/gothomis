<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_important_investigation extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','hb','height','bp','sugar_in_urine','scisorian_section',
    'age_under_twenty','age_above_thirty_five'];
}