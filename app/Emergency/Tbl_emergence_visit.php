<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_emergence_visit extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['emergency_arrival','referred_by','chief_complaint','triage_impression',
        'disposition','condition_dispo','mode_departure','arrival','visit_type',
    'acuity','rm','emmergency_dispo','time_left','patient_id','registered_by',
    'facility_id','date_attended','time_attended','dispo_decision'];
}