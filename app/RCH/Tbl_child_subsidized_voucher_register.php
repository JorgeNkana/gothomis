<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_subsidized_voucher_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','voucher_given','date'];
}