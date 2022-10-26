<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_prescription extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['conservatives','item_id','admission_id','verifier_id','visit_id','patient_id','prescriber_id','dispenser_id','quantity','frequency',
        'duration','dose','start_date','instruction','dispensing_status','out_of_stock','cancellation_reason'];
}