<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_transaction_type extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['transaction_type','adjustment','code','description','additive'];
}