<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_sub_store extends Model
{
    //
//use \App\UuidForKey; 
    protected $fillable=['item_id','batch_no','quantity','user_id','received_from_id','issued_store_id','requested_store_id','quantity_issued','request_amount',
    'transaction_type_id','request_status_id','order_no','control'];
}