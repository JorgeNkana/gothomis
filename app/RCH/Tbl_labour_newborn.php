<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_labour_newborn extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['client_id','facility_id','newborn_weight','user_id','gender','first_minute_score','fifth_minute_score',
        'breast_feeding_within_hour','arv_given'];
}