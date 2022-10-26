<?php

namespace App\RCH;

use Illuminate\Database\Eloquent\Model;

class Tbl_child_mother_detail extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['patient_id','facility_id','user_id','tt_given','vvu_status'];
}