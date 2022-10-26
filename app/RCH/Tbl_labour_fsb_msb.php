<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_fsb_msb extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','fsb_msb'];
}