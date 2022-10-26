<?php

namespace App\Facility;

use Illuminate\Database\Eloquent\Model;

class Tbl_facility extends Model
{
    //
//use \App\UuidForKey; 
    protected $fillable=['facility_code','facility_name','facility_type_id','address','mobile_number','email','council_id','region_id'];
}