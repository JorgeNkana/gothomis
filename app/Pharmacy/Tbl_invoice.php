<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tbl_invoice extends Model
{
    //
   
//use \App\UuidForKey; 
    protected $fillable=['invoice_number','vendor_id'];
}