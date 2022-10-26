<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_anti_natal_register extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','user_id','serial_no','year','status',
        'voucher_no','dob','occupation_id','residence_id','client_name','height','education'];
}