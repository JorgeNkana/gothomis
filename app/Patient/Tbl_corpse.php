<?php

namespace App\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_corpse extends Model
{
    //
	//use \App\UuidForKey; 
	 protected  $fillable=['first_name','middle_name','relationship_taker','description','funeral_site_id','residence_taker','status','discharge_info_by','residence_found','identity_type_taker',
'identity_number_taker','transport_taking','corpse_properties','corpse_brought_by','last_name','immediate_cause','underlying_cause','dob','gender','corpse_record_number'
	 ,'mobile_number','residence_id','marital_id','corpse_conditions','corpse_taken_by','occupation_id'
	 ,'tribe_id','country_id','kin','transport','death_certifier',
         'time_of_death_certifier','facility_id','police_mobile_no','police_station','driver','dod','user_id'
         ,'storage_reason','diagnosis_id','diagnosis_code'
];
}