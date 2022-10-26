<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_store_list extends Model
{
 //use \App\UuidForKey; 
    protected $fillable=['store_name','store_type_id','facility_id'];
}