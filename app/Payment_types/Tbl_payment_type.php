<?php

namespace App\Payment_types;

use Illuminate\Database\Eloquent\Model;

class Tbl_payment_type extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['payment_type_name','facility_id'];
}