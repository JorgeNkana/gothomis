<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_vaccination_register extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['vaccination_name','vaccination_type','dose'];
}