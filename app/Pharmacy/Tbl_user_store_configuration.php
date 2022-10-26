<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_user_store_configuration extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['user_id','store_id','status'];
}