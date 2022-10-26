<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_intra_opcondition extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['time_taken','am_pm','erasor','admission_id','request_id','information_category','noted_value','nurse_id'];
}