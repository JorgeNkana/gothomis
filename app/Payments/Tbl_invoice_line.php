<?php

namespace App\Payments;

use Illuminate\Database\Eloquent\Model;

class Tbl_invoice_line extends Model
{
	//use \App\UuidForKey; 
    protected $fillable =['invoice_id','item_type_id','quantity','item_price_id','user_id','patient_id','status_id','corpse_id',
        'facility_id','discount','discount_by','payment_filter','payment_method_id','gepg_receipt'];
}