<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_prescription extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['item_id','admission_id','visit_id','patient_id','prescriber_id','verifier_id','dispenser_id','quantity','frequency','duration',
        'dose','start_date','instruction','dispensing_status','cancellation_reason','out_of_stock'];
}