<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_vendor extends Model
{
//use \App\UuidForKey; 
    protected $fillable=[
        'vendor_code','vendor_name','vendor_address','vendor_phone_number','vendor_contact_person','facility_id'
    ];
}