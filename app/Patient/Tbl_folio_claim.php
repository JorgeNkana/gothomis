<?php

namespace App\patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_folio_claim extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=[
        'PatientFile','ClaimFile','PatientTypeCode','receiver_status','is_follio_submitted','SerialNo','FolioNo','ClaimMonth','ClaimYear','patient_id','patient_id','admission_id','PractitionerNo','user_created','FolioID','CardNo_id','Facility_id'
    ];
}