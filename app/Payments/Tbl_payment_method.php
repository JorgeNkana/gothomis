<?php

namespace App\Payments;

use Illuminate\Database\Eloquent\Model;

class Tbl_payment_method extends Model
{
	////use \App\UuidForKey; 
    protected $fillable = ['payment_method'];
}