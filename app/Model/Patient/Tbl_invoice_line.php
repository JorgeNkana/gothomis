<?php

namespace App\Model\Patient;

use Illuminate\Database\Eloquent\Model;

class Tbl_invoice_line extends Model
{
   //use \App\UuidForKey; 
    protected $fillable=[
       'insuarance_item_price_id', 'invoice_id','corpse_id','payment_filter','item_type_id','quantity','item_price_id','user_id',
        'patient_id','status_id','facility_id','discount','discount_by'
    ];
}