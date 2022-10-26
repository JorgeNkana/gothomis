<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_dispenser extends Model
{
    //
  //use \App\UuidForKey; 

    protected  $fillable=['received_from_id','batch_no','patient_id','request_amount','user_id','item_id','quantity_received','transaction_type_dispensed_id','dispenser_id','dispensing_status_id', 'quantity_dispensed','control'];
}