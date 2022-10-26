<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;


class Tbl_referral extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','visit_id','from_facility_id','to_facility_id','referral_type','status','sender_id','summary',"referral_date","referral_time","name","gender","reg","age",'feedback',"referral_code",
  "diagnosis",
  "temperature",
  "heart_rate",
  "respiratory_rate",
  "bp",
  "mental_status",
  "alert",
  "pertinent",
  "history",
  "chronic_ediction",
  "allergy",
  "lab_result",
  "radiology_result",
  "treatment",
  "contact_person"];
}