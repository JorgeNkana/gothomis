<?php

namespace App\Council;

use Illuminate\Database\Eloquent\Model;

class Tbl_council extends Model
{
	////use \App\UuidForKey; 
    //
    protected $fillable=['id','council_code','council_name','council_types_id','regions_id'];
}