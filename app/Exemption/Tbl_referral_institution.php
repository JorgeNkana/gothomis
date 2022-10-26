<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_referral_institution extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['institution_name','institution_type'];
}