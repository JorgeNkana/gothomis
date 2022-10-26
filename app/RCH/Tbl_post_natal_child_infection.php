<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_post_natal_child_infection extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','navel','skin','mouth','eye','jaundice','high_infection','gender'];
}