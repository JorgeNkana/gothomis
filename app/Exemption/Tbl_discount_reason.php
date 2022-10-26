<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_discount_reason extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['discount_reason','receipt_number','patient_id','facility_id'];
}