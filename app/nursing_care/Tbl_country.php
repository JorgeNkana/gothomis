<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_country extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['country_name','country_zone_id'];
}