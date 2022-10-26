<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_delivery_child_arv extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','arv_given'];
}