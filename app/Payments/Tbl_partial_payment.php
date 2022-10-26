<?php

namespace App\Payments;

use Illuminate\Database\Eloquent\Model;

class Tbl_partial_payment extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['user_id','patient_id','facility_id','invoice_id','status','visit_date_id','amount_billed','amount_paid'];
}