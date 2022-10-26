<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_sample_number_control extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['sample_no','facility_id','user_id'];
}