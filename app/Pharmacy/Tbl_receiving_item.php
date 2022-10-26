<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_receiving_item extends Model
{
    //
//use \App\UuidForKey; 
    protected  $fillable=['item_id','received_store_id','invoice_refference','transaction_type_id','requesting_store_id',
        'batch_no','remarks','Reorder_level','quantity','requested_amount','received_date','expiry_date','price','user_id','received_from_id',
        'facility_id','attachment_id','request_status_id','internal_issuer_id','issued_quantity','order_no','control','control_in'];





}