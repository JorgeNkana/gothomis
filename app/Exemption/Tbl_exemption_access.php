<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_exemption_access extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['exempt_id','user_id','status'];
}